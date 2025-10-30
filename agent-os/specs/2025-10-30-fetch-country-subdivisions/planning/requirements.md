# Spec Requirements: Fetch Country Subdivisions

## Initial Description
Now that we have implemented the base data model for CountrySubdivision, when we run the InstallCommand and after we create the countries, we need to fetch the country subdivisions from the OpenHolidays API.

## Requirements Discussion

### First Round Questions

**Q1: Integration Point** - Should this subdivision fetching run as part of the existing InstallCommand (synchronous during installation), or as a separate command/job (for running independently)?
**Answer:** As a second `spin()` block for now (synchronous during installation)

**Q2: Countries to Fetch** - Should we fetch subdivisions for all countries, or start with a specific subset (e.g., specific regions or high-priority countries)?
**Answer:** Start by only fetching Portugal and Spain

**Q3: Subdivision Type Mapping** - The OpenHolidays API returns subdivision types. Should we create a mapping configuration to standardize types across different countries, or use the types as-is from the API?
**Answer:** Create a mapping configuration. Strategy while testing: go through all countries and save each country subdivision response in the Saloon Fixtures folder to identify all the types.

**Q4: Nested Subdivisions Handling** - How deep should the nesting go? Should we fetch all levels of subdivisions (country → state → region → city, etc.), or limit to a specific depth?
**Answer:** Look at the `CreateRootCountrySubdivision` action. We will need to create a map/adapter from API responses to data objects.

**Q5: Error Handling** - If fetching subdivisions for a particular country fails (API error, rate limit, etc.), should we skip that country and continue, or halt the entire installation?
**Answer:** Skip that country and continue with others, and log the error

**Q6: Code Field** - For the `code` field in CountrySubdivision, should we use the short subdivision code (e.g., "PT-11" → "11") or the full ISO code?
**Answer:** Use the full ISO code

**Q7: Official Languages** - If a subdivision's official languages differ from the parent country, should we inherit from the parent or fetch/parse from the API response?
**Answer:** When empty, parse as comma-separated list and default to the parent country's languages

**Q8: Rate Limiting** - Should we implement rate limiting or delays between API requests to avoid hitting OpenHolidays API limits?
**Answer:** Implement a rate limit

**Q9: Existing vs. New Subdivisions** - If we run the install command multiple times, should we skip existing subdivisions or update them (upsert)?
**Answer:** Upsert them

**Q10: Are there any subdivisions or edge cases we should explicitly exclude?** - Any specific exclusions to be aware of?
**Answer:** None specified

### Existing Code to Reference

**Similar Features Identified:**
- Feature: Country creation and processing - The user indicated to look at how countries are being processed
- Action: `app/Actions/CountrySubdivision/CreateRootCountrySubdivision.php` - This action handles creating root subdivisions with nested children recursively. It accepts `CreateCountrySubdivisionData` objects and creates the hierarchy in a database transaction.
- Note: User mentioned "We still don't have a service to handle this" - meaning no existing service layer for API fetching/processing exists yet

**Key patterns from CreateRootCountrySubdivision:**
- Uses dependency injection for `CreateCountrySubdivision` action
- Wraps operations in `DB::transaction()` for data integrity
- Recursively processes nested children via `createChildren()` method
- Separates children data before creating parent, then creates children with parent_id reference
- Accepts `CreateCountrySubdivisionData` objects with optional children collection

## Visual Assets

### Files Provided:
No visual assets provided.

### Visual Insights:
N/A

## Requirements Summary

### Functional Requirements
- Fetch country subdivisions from OpenHolidays API during InstallCommand execution
- Add a second `spin()` block (synchronous) after country creation
- Initially fetch subdivisions for Portugal and Spain only
- Support nested subdivision hierarchies (unlimited depth based on API response)
- Map API subdivision types to standardized types using a configuration
- Use full ISO codes for the `code` field (e.g., "PT-11" not "11")
- Parse official languages as comma-separated list, defaulting to parent country languages when empty
- Upsert subdivisions on repeated installations (update existing, create new)
- Skip countries that fail with errors and continue processing others
- Log errors for failed countries
- Implement rate limiting between API requests
- Save API responses to Saloon Fixtures folder during testing to identify subdivision types

### Reusability Opportunities
- Reference existing country creation/processing patterns
- Use `CreateRootCountrySubdivision` action as template for understanding data structure and nesting
- Leverage existing `CreateCountrySubdivisionData` data object
- Follow existing Action pattern (not Service layer, as none exists yet)
- Use existing Saloon HTTP client setup for OpenHolidays API integration

### Scope Boundaries

**In Scope:**
- Fetching subdivisions from OpenHolidays API
- Creating mapping/adapter from API responses to `CreateCountrySubdivisionData` objects
- Implementing subdivision type mapping configuration
- Handling nested subdivision hierarchies recursively
- Upserting subdivisions (create or update)
- Error handling with logging and graceful continuation
- Rate limiting API requests
- Initial implementation for Portugal and Spain
- Saving API fixtures for testing and type identification

**Out of Scope:**
- Fetching subdivisions for all countries (start with PT and ES)
- Creating a generic Service layer (follow existing Action pattern)
- Background job processing (synchronous during installation)
- User interface for managing subdivisions
- Manual subdivision management features
- Subdivision validation beyond what API provides

### Technical Considerations
- Integration point: InstallCommand with second `spin()` block
- API: OpenHolidays API for subdivision data
- HTTP Client: Saloon (already in use for countries)
- Data structure: `CreateCountrySubdivisionData` with nested children collection
- Existing action: `CreateRootCountrySubdivision` handles recursive nesting in transactions
- Need to create: Adapter/mapper from API response to Data objects
- Need to create: Type mapping configuration for subdivision types
- Need to create: Upsert action for subdivisions (or update existing CreateCountrySubdivision)
- Testing strategy: Save API responses as fixtures to identify all subdivision types
- Error handling: Log and continue (don't halt installation)
- Rate limiting: Implement delays between requests
- Database: Use transactions for data integrity (follow CreateRootCountrySubdivision pattern)
