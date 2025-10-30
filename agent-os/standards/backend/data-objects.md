## Data object standards (Spatie Laravel Data)

- **DTOs Not Validation**: Data objects are DTOs for type-safe data transfer - NOT validation layers (validation belongs in Form Requests)
- **Store in app/Data/**: All Data objects live in `app/Data/` namespace with descriptive subfolders
- **Suffix with Data**: ALL Data objects MUST be suffixed with `Data` (e.g., `UpdateOfficeData`, `CreateUserData`, `AddressData`)
- **Add toArray() Annotation**: ALWAYS add `@method array<string, mixed> toArray()` PHPDoc annotation to all Data objects
- **Readonly Properties**: Use `readonly` keyword for all properties - Data objects are immutable
- **Optional for Updates**: Use `Type|Optional` for update Data objects to support partial updates
- **Nullable vs Optional**: `Optional` = field not provided (don't update), `null` = explicitly set to null (update to null)
- **Required for Creates**: Use required types for create Data objects to ensure all fields provided
- **Use SnakeCaseMapper**: Add `#[MapOutputName(SnakeCaseMapper::class)]` for internal data consistency
- **Use CamelCaseMapper for Frontend**: Add `#[MapOutputName(CamelCaseMapper::class)]` when passing data to Inertia/React components
- **Computed Properties**: Use `#[Computed]` attribute for derived properties - declare as properties (not methods) and initialize in constructor
- **Create from Validated Data**: Use `DataObject::from($request->validated())` in controllers
- **Nested Data Objects**: Use nested Data objects for related entities (e.g., Office with AddressData)
- **No Validation Attributes**: Do NOT add validation attributes to Data objects - validation belongs in Form Requests
- **Test Optional Handling**: ALWAYS test that Optional fields are excluded from `toArray()` output