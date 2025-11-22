# Data Model: Approvals Scaffold

**Branch**: `001-approvals-scaffold` | **Date**: 2025-11-22

## Entities

### Employee (existing - modified)

Add self-referential relationship for manager.

**New Fields**:

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| manager_id | foreignId | nullable, references employees.id | Employee's direct manager |

**New Relationships**:

```php
public function manager(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'manager_id');
}

public function directReports(): HasMany
{
    return $this->hasMany(Employee::class, 'manager_id');
}
```

---

### Approval (new)

Polymorphic model for approval decisions on any approvable entity.

**Migration Column Order** (per constitution):

```php
Schema::create('approvals', function (Blueprint $table) {
    $table->id();
    $table->timestamps();

    $table->foreignIdFor(Employee::class, 'approved_by')->nullable()->constrained('employees');

    $table->string('approvable_type');
    $table->unsignedBigInteger('approvable_id');
    $table->string('status');
    $table->timestamp('approved_at')->nullable();
    $table->text('rejection_reason')->nullable();

    $table->index(['approvable_type', 'approvable_id']);
    $table->index('status');
});
```

**Fields**:

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | id | primary | |
| created_at | timestamp | | |
| updated_at | timestamp | | |
| approved_by | foreignId | nullable, references employees.id | Approver |
| approvable_type | string | required | Morph class |
| approvable_id | unsignedBigInteger | required | ID of approvable entity |
| status | string (enum) | required | pending, approved, rejected, cancelled |
| approved_at | timestamp | nullable | Decision timestamp |
| rejection_reason | text | nullable | Reason for rejection |

**Relationships**:

```php
public function approvable(): MorphTo
{
    return $this->morphTo();
}

public function approver(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'approved_by');
}
```

**State Transitions**:

```
pending → approved (manager action)
pending → rejected (manager action)
pending → cancelled (employee action)
```

---

### TimeOffRequest (existing - modified)

**New Relationships**:

```php
public function approval(): MorphOne
{
    return $this->morphOne(Approval::class, 'approvable');
}
```

---

## Migration Order

1. `add_manager_id_to_employees_table` - Add manager_id foreign key
2. `create_approvals_table` - Create polymorphic approvals table

---

## Seeder Updates

### EmployeeSeeder

Create manager hierarchy:
- Owner (no manager)
- HR Manager → reports to Owner
- Department Manager → reports to Owner
- Employees → report to managers
