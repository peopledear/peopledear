# Task Breakdown: Fetch Country Subdivisions from OpenHolidays API

## Overview
Total Task Groups: 6
Total Tasks: 25

This feature integrates OpenHolidays API subdivision fetching into the InstallCommand to automatically populate CountrySubdivision records for Portugal and Spain during installation, with proper type mapping, nested hierarchy support, and error handling.

## Task List

### Phase 1: Configuration & Setup

#### Task Group 1: Type Mapping Configuration & Upsert Investigation
**Dependencies:** None

- [x] 1.0 Complete subdivision type mapping configuration and upsert investigation
  - [x] 1.1 Investigate CreateCountrySubdivision for upsert support
    - Check if `iso_code` has unique constraint in `country_subdivisions` table
    - Review if `CreateCountrySubdivision` needs updating from `create()` to `updateOrCreate()`
    - If upsert needed, match on `iso_code` field
    - Write 1-2 tests for upsert behavior (create new, update existing)
    - Update action if necessary to support re-running installation
  - [x] 1.2 Write 2-4 focused tests for OpenHolidaysSubdivisionType enum
    - Test transform() method maps to correct CountrySubdivisionType
    - Test fallback/default type handling
    - Test all expected subdivision types from fixtures
  - [x] 1.3 Create OpenHolidaysSubdivisionType enum
    - File: `app/Enums/Integrations/OpenHolidays/OpenHolidaysSubdivisionType.php`
    - Follow pattern from OpenHolidaysHolidayType.php
    - Add cases for ALL 7 API categories from fixtures:
      - `Distrito` → `CountrySubdivisionType::District` (Portugal)
      - `Municipio` → `CountrySubdivisionType::Municipality` (Portugal)
      - `RegiaoAutonoma` → `CountrySubdivisionType::AutonomousRegion` (Portugal)
      - `ComunidadAutonoma` → `CountrySubdivisionType::AutonomousRegion` (Spain)
      - `Provincia` → `CountrySubdivisionType::Province` (Spain)
      - `CiudadAutonoma` → `CountrySubdivisionType::City` (Spain - Ceuta & Melilla)
      - `ComunidadDeMadrid` → `CountrySubdivisionType::Community` (Spain - special case)
    - Implement transform() method returning CountrySubdivisionType
    - Add sensible fallback for unmapped types (default to District)
  - [x] 1.4 Add rate limiting configuration to config/openholidays.php
    - Add 'rate_limit' array with 'delay_ms' key (default: 500)
    - Document purpose: prevent API rate limiting between country requests
    - Place in configuration alongside existing cache settings
  - [x] 1.5 Ensure type mapping tests pass
    - Run ONLY the 2-4 tests written in 1.1
    - Verify enum cases map correctly to CountrySubdivisionType
    - Do NOT run entire test suite at this stage

**Acceptance Criteria:**
- OpenHolidaysSubdivisionType enum created with transform() method
- The 2-4 tests written in 1.1 pass
- Rate limiting configuration added to config file
- Enum follows existing OpenHolidaysHolidayType pattern

**Technical Notes:**
- Subdivision type mapping may need updates after analyzing API fixtures in Phase 5
- Rate limit delay should be configurable for different environments

---

### Phase 2: API Data Adapter

#### Task Group 2: Subdivision Adapter Implementation
**Dependencies:** Task Group 1

- [x] 2.0 Complete API data adapter
  - [x] 2.1 Write 2-6 focused tests for OpenHolidaysSubdivisionAdapter
    - Test toCreateData() transforms simple subdivision correctly
    - Test nested children are recursively transformed
    - Test official languages parsing (comma-separated to array)
    - Test official languages inheritance from country when empty
    - Test full ISO code preservation in both code and isoCode fields
  - [x] 2.2 Create generic Adapter contract/interface
    - File: `app/Contracts/Adapter.php`
    - Created generic interface following HolidayAdapter pattern but more flexible
    - Supports array context for passing countryId and countryLanguages
    - Uses PHPDoc templates for type safety
  - [x] 2.3 Implement OpenHolidaysSubdivisionAdapter
    - File: `app/Http/Integrations/OpenHolidays/Adapters/OpenHolidaysSubdivisionAdapter.php`
    - Implements Adapter interface with both array context and named parameters
    - Maps OpenHolidaysSubdivisionData to CreateCountrySubdivisionData
    - Official languages are already arrays from API (no comma-split needed)
    - Inherits parent country languages when subdivision languages empty/null
    - Preserves full ISO code (e.g., "PT-11") in both code and isoCode
    - Builds name array from localized names in API response
    - Recursively transforms children maintaining hierarchy
    - Uses OpenHolidaysSubdivisionType::from()->transform()
  - [x] 2.4 Add helper method for language parsing
    - Private method parseOfficialLanguages(array|null, array)
    - Returns subdivision languages when provided, or country default
    - Handles null/empty cases gracefully
  - [x] 2.5 Add helper method for recursive children transformation
    - Private method transformChildren(array|null, int, array)
    - Returns Collection<int, CreateCountrySubdivisionData>|null
    - Recursively processes nested subdivisions
    - Passes country ID and languages to children
  - [x] 2.6 Ensure adapter tests pass
    - All 5 tests pass (32 assertions)
    - Verified transformations work correctly
    - Code formatted with Pint

**Acceptance Criteria:**
- Generic Adapter contract created following HolidayAdapter pattern
- OpenHolidaysSubdivisionAdapter implements Adapter correctly
- All 5 existing tests pass (32 assertions)
- Adapter handles nested children recursively
- Official languages parsed and inherited correctly
- Full ISO codes preserved

**Technical Notes:**
- Adapter pattern reuses from OpenHolidaysHolidayAdapter structure
- Language inheritance ensures subdivisions always have valid language data
- Recursive transformation maintains unlimited nesting depth
- Implementation supports both array context (Adapter interface) and named parameters (test usage)

---

### Phase 3: InstallCommand Integration

#### Task Group 3: Command Integration with CreateRootCountrySubdivision
**Dependencies:** Task Group 2

- [x] 3.0 Complete InstallCommand integration
  - [x] 3.1 Write 2-4 focused tests for InstallCommand subdivision fetching
    - Test subdivisions fetched for Portugal and Spain
    - Test progress message displayed: "Fetching country subdivisions..."
    - Test graceful handling when API fails for one country
    - Test rate limiting delay applied between requests
  - [x] 3.2 Update InstallCommand handle() method
    - File: `app/Console/Commands/InstallCommand.php`
    - Inject CreateRootCountrySubdivision action via constructor
    - Inject OpenHolidaysSubdivisionAdapter via constructor
    - Inject OpenHolidaysConnector via constructor
    - Add second spin() block after country seeding
    - Spin message: "Fetching country subdivisions..."
  - [x] 3.3 Implement subdivision fetching logic
    - Iterate over ['PT', 'ES'] country ISO codes
    - Retrieve Country model from database to get country ID and languages
    - Create GetSubdivisionsRequest with country ISO code
    - Send request via OpenHolidaysConnector->send()
    - Parse JSON response to get array/collection of root subdivisions
    - For each root subdivision from API:
      - Transform using adapter->toCreateData() (returns CreateCountrySubdivisionData with nested children)
      - Call CreateRootCountrySubdivision->handle() passing the transformed data
      - Orchestrator handles ALL recursion, transactions, and persistence automatically
  - [x] 3.4 Apply rate limiting between country requests
    - Use config('openholidays.rate_limit.delay_ms', 500)
    - Call usleep(delay * 1000) between country iterations
    - Skip delay on last country to avoid unnecessary wait
  - [x] 3.5 Wrap each country fetch in try-catch
    - Catch Throwable to handle API errors gracefully
    - Log error with context: country ISO, exception message
    - Continue processing next country on failure
    - Use Log::error() with structured context array
  - [x] 3.6 Ensure InstallCommand tests pass
    - Run ONLY the 2-4 tests written in 3.1
    - Verify integration works correctly
    - Do NOT run entire test suite at this stage

**Acceptance Criteria:**
- The 2-4 tests written in 3.1 pass
- InstallCommand has second spin block for subdivisions
- Subdivisions fetched only for Portugal and Spain
- CreateRootCountrySubdivision orchestrates the entire process
- Rate limiting applied between API requests
- Errors logged and processing continues for other countries
- Progress feedback provided to user

**Technical Notes:**
- CreateRootCountrySubdivision handles DB transactions and recursion
- Adapter transforms API data to CreateCountrySubdivisionData
- No new upsert action needed - CreateRootCountrySubdivision uses CreateCountrySubdivision
- Error handling ensures installation doesn't fail on API issues

---

### Phase 4: Error Handling & Logging

#### Task Group 4: Robust Error Management
**Dependencies:** Task Group 3

- [ ] 4.0 Complete error handling and logging
  - [ ] 4.1 Write 2-4 focused tests for error scenarios
    - Test API connection failure logs error and continues
    - Test invalid JSON response logs error and continues
    - Test missing country in database logs error and continues
    - Test all errors include proper context (country ISO, message)
  - [ ] 4.2 Enhance error logging with structured context
    - Log level: error
    - Context: country ISO code, exception message, exception class
    - Message format: "Failed to fetch subdivisions for {countryIsoCode}"
    - Include stack trace for debugging
  - [ ] 4.3 Add defensive null checks
    - Verify Country model exists before fetching subdivisions
    - Verify API response is not empty before processing
    - Verify subdivision data has required fields before transforming
    - Skip invalid subdivisions gracefully with warning logs
  - [ ] 4.4 Ensure error handling tests pass
    - Run ONLY the 2-4 tests written in 4.1
    - Verify errors are caught and logged correctly
    - Do NOT run entire test suite at this stage

**Acceptance Criteria:**
- The 2-4 tests written in 4.1 pass
- All error scenarios logged with proper context
- Defensive null checks prevent unexpected crashes
- Processing continues for other countries on errors
- Warning logs for skipped invalid subdivisions

**Technical Notes:**
- Error handling ensures installation robustness
- Structured logging aids debugging in production
- Graceful degradation: partial success is acceptable

---

### Phase 5: Testing with Saloon Fixtures

#### Task Group 5: Comprehensive Test Coverage with Fixtures
**Dependencies:** Task Groups 1-4

- [ ] 5.0 Complete testing with Saloon fixtures and fill critical gaps only
  - [ ] 5.1 Review existing tests from Task Groups 1-4
    - Review the 2-4 tests written for type mapping (Task 1.1)
    - Review the 2-6 tests written for adapter (Task 2.1)
    - Review the 2-4 tests written for InstallCommand (Task 3.1)
    - Review the 2-4 tests written for error handling (Task 4.1)
    - Total existing tests: approximately 8-18 tests
  - [ ] 5.2 Fetch and save API responses as Saloon fixtures
    - Create test script to fetch real API responses
    - File: `tests/Fixtures/Saloon/OpenHolidays/portugal-subdivisions.json`
    - File: `tests/Fixtures/Saloon/OpenHolidays/spain-subdivisions.json`
    - Use GetSubdivisionsRequest with PT and ES country codes
    - Save raw JSON responses maintaining structure
    - Analyze responses to identify all subdivision type strings
  - [ ] 5.3 Update OpenHolidaysSubdivisionType enum based on fixtures
    - Review fixture data for actual subdivision type strings
    - Add missing enum cases discovered in fixtures
    - Update transform() method to map all discovered types
    - Document any unknown types in code comments
    - Update tests to cover newly discovered types
  - [ ] 5.4 Analyze test coverage gaps for THIS feature only
    - Identify critical workflows lacking test coverage
    - Focus ONLY on gaps related to subdivision fetching feature
    - Prioritize end-to-end workflows over unit test gaps
    - Do NOT assess entire application test coverage
  - [ ] 5.5 Write up to 6 additional strategic tests maximum
    - Add maximum of 6 new tests to fill identified critical gaps
    - Focus on integration points and end-to-end workflows
    - Test with fixture data for realistic scenarios
    - Test nested hierarchy creation with multiple levels
    - Test subdivision upsert behavior on repeated runs
    - Skip edge cases, performance tests unless business-critical
  - [ ] 5.6 Run feature-specific tests only
    - Run ONLY tests related to subdivision fetching feature
    - Expected total: approximately 14-24 tests maximum
    - Do NOT run entire application test suite
    - Verify all critical workflows pass

**Acceptance Criteria:**
- Portugal and Spain subdivision fixtures saved successfully
- OpenHolidaysSubdivisionType enum updated with all discovered types
- All feature-specific tests pass (approximately 14-24 tests total)
- Critical workflows covered with realistic fixture data
- No more than 6 additional tests added when filling gaps
- Testing focused exclusively on subdivision fetching feature

**Technical Notes:**
- Fixtures provide realistic test data and document API structure
- Type mapping completeness depends on fixture analysis
- Feature tests use fixtures for deterministic, fast testing
- Saloon's caching mechanism helps with fixture management

---

### Phase 6: Documentation

#### Task Group 6: Code Documentation
**Dependencies:** Task Group 5

- [ ] 6.0 Complete code documentation
  - [ ] 6.1 Document OpenHolidaysSubdivisionType enum
    - Add PHPDoc block explaining purpose
    - Document each enum case with country examples
    - Document transform() method behavior
    - Document fallback strategy for unknown types
  - [ ] 6.2 Document OpenHolidaysSubdivisionAdapter class
    - Add class-level PHPDoc explaining adapter pattern
    - Document toCreateData() method parameters and return
    - Document private helper methods
    - Add inline comments for complex transformations
  - [ ] 6.3 Document InstallCommand subdivision integration
    - Add inline comments explaining subdivision fetching flow
    - Document error handling strategy
    - Document rate limiting implementation
    - Add comments explaining CreateRootCountrySubdivision usage
  - [ ] 6.4 Update config/openholidays.php documentation
    - Document rate_limit configuration options
    - Explain rate limiting purpose and recommended values
    - Add examples for different environments
  - [ ] 6.5 Create type mapping reference document
    - File: `agent-os/specs/2025-10-30-fetch-country-subdivisions/planning/subdivision-type-mapping.md`
    - List all OpenHolidays API subdivision types discovered
    - Map each to corresponding CountrySubdivisionType
    - Document any unmapped types and fallback strategy
    - Include examples from Portugal and Spain

**Acceptance Criteria:**
- All new classes have comprehensive PHPDoc blocks
- Complex logic has inline comments for clarity
- Configuration options documented with examples
- Type mapping reference document created
- Documentation follows existing codebase conventions

**Technical Notes:**
- Documentation aids future maintenance and debugging
- Type mapping reference helps understand API variations
- Clear comments reduce cognitive load for developers

---

## Execution Order

Recommended implementation sequence:
1. **Phase 1: Configuration & Setup** (Task Group 1) - No dependencies, foundational setup
2. **Phase 2: API Data Adapter** (Task Group 2) - Depends on type mapping configuration
3. **Phase 3: InstallCommand Integration** (Task Group 3) - Depends on adapter being ready
4. **Phase 4: Error Handling & Logging** (Task Group 4) - Depends on basic integration working
5. **Phase 5: Testing with Fixtures** (Task Group 5) - Depends on all implementation complete
6. **Phase 6: Documentation** (Task Group 6) - Final step after everything works

## Key Technical Decisions

**Action Pattern Architecture:**
- `CreateRootCountrySubdivision` - Orchestrates the entire process (handles recursion, hierarchy, transactions)
- `CreateCountrySubdivision` - Low-level persistence (saves single subdivision to database)
- No new upsert action needed - existing actions handle all requirements

**Implementation Flow:**
```
InstallCommand (spin block #2)
  → Iterate PT, ES countries
  → Fetch from OpenHolidays API (GetSubdivisionsRequest)
  → OpenHolidaysSubdivisionAdapter transforms to CreateCountrySubdivisionData
  → Call CreateRootCountrySubdivision (orchestrates entire hierarchy)
    → Strips children from parent data
    → Calls CreateCountrySubdivision (saves parent to DB)
    → Recursively processes children via createChildren()
      → For each child, strips grandchildren
      → Calls CreateCountrySubdivision (saves child with parent_id)
      → Recursively processes grandchildren
  → Rate limiting delay between countries
  → Error handling per country with logging
```

**Critical Integration Points:**
- InstallCommand integrates after country seeding completes
- Adapter transforms nested API data to nested CreateCountrySubdivisionData
- CreateRootCountrySubdivision handles DB transactions and recursion automatically
- Error handling ensures installation robustness

**Testing Strategy:**
- Limit test writing during development: 2-6 tests per task group
- Focus on critical behaviors only during development
- Use Saloon fixtures for realistic, deterministic testing
- Gap analysis in Phase 5 adds maximum 6 additional strategic tests
- Total expected tests: approximately 14-24 tests for entire feature
- Run only feature-specific tests, not entire suite

## Files to Create/Modify

**New Files:**
- `app/Enums/Integrations/OpenHolidays/OpenHolidaysSubdivisionType.php`
- `app/Contracts/Adapter.php`
- `app/Http/Integrations/OpenHolidays/Adapters/OpenHolidaysSubdivisionAdapter.php`
- `tests/Fixtures/Saloon/OpenHolidays/portugal-subdivisions.json`
- `tests/Fixtures/Saloon/OpenHolidays/spain-subdivisions.json`
- `agent-os/specs/2025-10-30-fetch-country-subdivisions/planning/subdivision-type-mapping.md`

**Modified Files:**
- `app/Console/Commands/InstallCommand.php` - Add second spin block for subdivision fetching
- `config/openholidays.php` - Add rate limiting configuration

**Test Files:**
- `tests/Unit/Enums/Integrations/OpenHolidays/OpenHolidaysSubdivisionTypeTest.php`
- `tests/Unit/Http/Integrations/OpenHolidays/Adapters/OpenHolidaysSubdivisionAdapterTest.php`
- `tests/Feature/Console/Commands/InstallCommandTest.php` - Add subdivision fetching tests
- Additional strategic tests as needed in Phase 5