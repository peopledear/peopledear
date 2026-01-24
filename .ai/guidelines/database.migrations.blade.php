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

@verbatim
<code-snippet name="Correct CREATE TABLE column order" lang="php">
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->string('name');
    $table->string('email')->unique();
    $table->boolean('is_active');
});
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Correct ALTER TABLE migration without after()" lang="php">
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable();
    // ✅ CORRECT - no after() method for PostgreSQL compatibility
});
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Correct migration structure without down()" lang="php">
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
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="CORRECT - Default in Model attributes" lang="php">
class User extends Model
{
    protected $attributes = [
        'is_active' => true, // ✅ Simple defaults belong here
    ];
}
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="CORRECT - Context-dependent default in Action" lang="php">
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
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Correct foreign keys without cascade" lang="php">
Schema::create('invitations', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->foreignIdFor(User::class, 'invited_by_id')->constrained('users');
    // ✅ Explicit table name when column name differs
    $table->foreignIdFor(Role::class);
    // ✅ Auto-infers 'role_id' and 'roles' table
    // ❌ NO ->onDelete('cascade') or ->onUpdate('cascade')
});
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Column modification preserving all attributes" lang="php">
Schema::table('users', function (Blueprint $table) {
    $table->string('email')->unique()->nullable()->change();
    // ✅ MUST include ALL previous attributes or they will be lost
});
</code-snippet>
@endverbatim
