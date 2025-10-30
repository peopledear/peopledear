# Specification: Fetch Country Subdivisions from OpenHolidays API

## Goal
Integrate subdivision fetching from OpenHolidays API into the InstallCommand to populate CountrySubdivision records for Portugal and Spain, supporting nested hierarchies and standardized type mapping.

## User Stories
- As a system administrator, I want subdivisions automatically fetched during installation so that the application has geographic hierarchy data available
- As a developer, I want subdivision data properly mapped and typed so that the system can accurately represent administrative divisions

## Specific Requirements

**API Integration with OpenHolidays**
- Use existing `GetSubdivisionsRequest` Saloon request for fetching subdivision data
- Leverage `OpenHolidaysConnector` with configured timeouts, caching, and error handling
- Pass country ISO code (PT, ES) as query parameter to API endpoint `/Subdivisions`
- Handle API responses containing nested subdivision hierarchies with unlimited depth
- Use existing Saloon caching mechanism (30-day TTL) to avoid repeated API calls

**InstallCommand Integration Point**
- Add second `spin()` block after countries are seeded in `InstallCommand::handle()`
- Execute synchronously during installation (no background jobs)
- Display progress message: "Fetching country subdivisions..."
- Only fetch subdivisions for Portugal (PT) and Spain (ES) initially
- Retrieve Country models from database to get country IDs for subdivision association

**Adapter Pattern for API Response Mapping**
- Create `OpenHolidaysSubdivisionAdapter` following pattern from `OpenHolidaysHolidayAdapter`
- Implement contract/interface defining `toCreateData()` method signature
- Map `OpenHolidaysSubdivisionData` to `CreateCountrySubdivisionData` with nested children
- Handle optional fields: officialLanguages (parse comma-separated or inherit from country)
- Preserve full ISO code format (e.g., "PT-11") in both `code` and `isoCode` fields
- Recursively transform nested children maintaining hierarchy structure

**Subdivision Type Mapping Configuration**
- Create enum mapping like app/Enums/Integrations/OpenHolidays/OpenHolidaysHolidayType.php OpenHolidays API type strings to `CountrySubdivisionType` enum
- Strategy: collect API responses as Saloon fixtures during testing to identify all type variations
- Store fixtures in `tests/Fixtures/Saloon/OpenHolidays/` directory following existing pattern
- Map common types: district, region, municipality, parish, community, province, etc.
- Default to sensible fallback type when API provides unmapped type string

**Official Languages Handling**
- Check if subdivision's `officialLanguages` field is empty/null in API response
- Parse comma-separated language codes when provided (e.g., "pt,es" becomes ["pt", "es"])
- Inherit from parent country's `official_languages` array when subdivision has no specific languages
- Store as array matching `CountrySubdivision` model's `official_languages` cast

**Orchestration Pattern with CreateRootCountrySubdivision**
- Use existing `CreateRootCountrySubdivision` action to orchestrate the entire process
- Action handles DB transactions, recursive processing, and parent-child relationships automatically
- Pass `CreateCountrySubdivisionData` with nested children collection to the action
- Action strips children before creating parent, then recursively processes children
- Each subdivision saved via `CreateCountrySubdivision` action (low-level persistence)
- No manual hierarchy flattening or ISO code tracking needed - orchestrator handles everything
- Support unlimited nesting levels as provided by API response

**Error Handling and Resilience**
- Wrap each country's subdivision fetch in try-catch block
- Log errors using Laravel's logging system with context (country ISO, exception message)
- Continue processing remaining countries when one fails (skip and proceed)
- Don't halt entire InstallCommand execution on subdivision fetch failures
- Provide meaningful error messages for debugging API issues

**Rate Limiting Implementation**
- Add configurable delay between API requests to avoid rate limits
- Use `sleep()` or Laravel's rate limiting helpers between country requests
- Configure delay in `config/openholidays.php` (suggested: 500ms-1000ms between requests)
- Consider existing caching mechanism reduces need for aggressive rate limiting

**Testing Strategy with Fixtures**
- Save API responses to `tests/Fixtures/Saloon/OpenHolidays/portugal-subdivisions.json`
- Save API responses to `tests/Fixtures/Saloon/OpenHolidays/spain-subdivisions.json`
- Analyze fixture responses to identify all subdivision type strings used
- Create comprehensive type mapping based on fixture analysis
- Write unit tests for adapter using fixture data
- Write feature test for InstallCommand including subdivision fetching

## Visual Design

No visual assets provided for this feature.

## Existing Code to Leverage

**`app/Console/Commands/InstallCommand.php`**
- Follow existing `spin()` pattern for visual feedback during long operations
- Inject new action via dependency injection in `handle()` method signature
- Add second spin block after country seeding completes
- Maintain consistent error handling approach with existing command structure

**`app/Actions/CountrySubdivision/CreateRootCountrySubdivision.php`** (Orchestration)
- This action orchestrates the entire subdivision creation process
- Handles DB transactions automatically via `DB::transaction()`
- Strips children from parent data before creating parent subdivision
- Recursively processes children via private `createChildren()` method
- Automatically sets `countrySubdivisionId` (parent_id) for child relationships
- Calls `CreateCountrySubdivision` action for each subdivision persistence
- Supports unlimited nesting depth through recursion

**`app/Actions/CountrySubdivision/CreateCountrySubdivision.php`** (Persistence)
- Low-level action that saves single subdivision to database
- Called by `CreateRootCountrySubdivision` for each subdivision in hierarchy
- Currently uses `create()` - **may need to be updated to `updateOrCreate()` for upsert support**
- Upsert should match on `iso_code` field to support re-running installation
- **Investigation needed**: Check if `iso_code` has unique constraint in database

**`app/Http/Integrations/OpenHolidays/Requests/GetSubdivisionsRequest.php`**
- Use existing Saloon request with `countryIsoCode` parameter
- Leverage built-in caching (30-day TTL) and Laravel cache driver
- Request already configured with proper endpoint `/Subdivisions`
- Optional `languageIsoCode` parameter available if needed

**`app/Http/Integrations/OpenHolidays/Adapters/OpenHolidaysHolidayAdapter.php`**
- Follow adapter pattern structure: implement interface with `toCreateData()` method
- Transform external API data object to internal Data object format
- Handle nullable/optional fields with safe navigation and defaults
- Use helper methods from Data objects for localized content

## Out of Scope

- Fetching subdivisions for countries other than Portugal and Spain
- Creating generic Service layer architecture (use Action pattern per existing conventions)
- Background job processing or queue-based subdivision fetching
- User interface for viewing, managing, or editing subdivisions manually
- Subdivision validation rules beyond what OpenHolidays API provides
- Custom subdivision creation or modification features
- Subdivision search or filtering functionality
- Exporting subdivision data to external formats
- Webhook or event-driven subdivision updates from external sources
- Multi-language subdivision name translation beyond API-provided data