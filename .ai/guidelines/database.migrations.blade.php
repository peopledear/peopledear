# Database Migration Guidelines

- **Column Order for CREATE TABLE**: ALWAYS use this exact order: `id()` first, then `timestamps()`, then all other columns
- **Column Order for ALTER TABLE**: Do NOT use `after()` method - simply add columns without position specification (using `after()` breaks PostgreSQL compatibility)
- **NEVER implement the `down()` method** - this application does not roll back migrations, always remove it
- Use `php artisan migrate:fresh --seed` to reset the database
- **NO default values in migrations** - default values are business logic, NOT database constraints
- Implement defaults in Model's `$attributes` property, Model's `booted()` method, Action classes, or Data Objects
- **ALWAYS use `foreignIdFor(Model::class)`** for foreign key columns
- Use the second parameter to customize column name: `foreignIdFor(User::class, 'invited_by_id')->constrained('users')`
- **NEVER add cascade constraints** - no `->onDelete('cascade')` or `->onUpdate('cascade')`
- Handle deletions explicitly in the application layer using Actions (cascading can lead to unintended data loss)
- When modifying a column, MUST include ALL attributes previously defined, otherwise they will be dropped

@boostsnippet('Correct CREATE TABLE column order', 'php')
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->string('name');
    $table->string('email')->unique();
    $table->boolean('is_active');
});
@endboostsnippet

@boostsnippet('Correct ALTER TABLE migration without after()', 'php')
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable();
    // ✅ CORRECT - no after() method for PostgreSQL compatibility
});
@endboostsnippet

@boostsnippet('Correct migration structure without down()', 'php')
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email');
            $table->foreignIdFor(User::class, 'invited_by_id')->constrained('users');
            $table->foreignIdFor(Role::class);
            $table->timestamp('accepted_at')->nullable();
        });
    }
};
@endboostsnippet

@boostsnippet('CORRECT - Default in Model attributes', 'php')
class User extends Model
{
    protected $attributes = [
        'is_active' => true, // ✅ Simple defaults belong here
    ];
}
@endboostsnippet

@boostsnippet('CORRECT - Context-dependent default in Action', 'php')
class CreateUser
{
    public function handle(string $email, string $name, ?int $roleId = null): User
    {
        return User::query()->create([
            'email' => $email,
            'name' => $name,
            'role_id' => $roleId ?? Role::query()->where('name', 'employee')->first()->id,
            // ✅ Business logic belongs in Actions
        ]);
    }
}
@endboostsnippet

@boostsnippet('Correct foreign keys without cascade', 'php')
Schema::create('invitations', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->foreignIdFor(User::class, 'invited_by_id')->constrained('users');
    // ✅ Explicit table name when column name differs
    $table->foreignIdFor(Role::class);
    // ✅ Auto-infers 'role_id' and 'roles' table
    // ❌ NO ->onDelete('cascade') or ->onUpdate('cascade')
});
@endboostsnippet

@boostsnippet('Column modification preserving all attributes', 'php')
Schema::table('users', function (Blueprint $table) {
    $table->string('email')->unique()->nullable()->change();
    // ✅ MUST include ALL previous attributes or they will be lost
});
@endboostsnippet
