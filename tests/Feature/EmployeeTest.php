<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();
        $this->authenticate();
    }

    public function test_index_returns_employees()
    {
        Employee::factory()->count(2)->create();

        $response = $this->getJson(route('api.employees.index'));

        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function test_store_creates_employee()
    {
        $company = Company::factory()->create();
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone_number' => '123456789',
            'company_id' => $company->id,
        ];

        $response = $this->postJson(route('api.employees.store'), $data);

        $response->assertCreated()
            ->assertJsonFragment(['email' => 'john.doe@example.com']);
        $this->assertDatabaseHas('employees', ['email' => 'john.doe@example.com']);
    }

    public function test_show_returns_employee()
    {
        $employee = Employee::factory()->create();

        $response = $this->getJson(route('api.employees.show', $employee));

        $response->assertOk()
            ->assertJsonFragment(['id' => $employee->id]);
    }

    public function test_update_modifies_employee()
    {
        $employee = Employee::factory()->create();
        $company = Company::factory()->create();

        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone_number' => '987654321',
            'company_id' => $company->id,
        ];

        $response = $this->putJson(route('api.employees.update', $employee), $data);

        $response->assertOk()
            ->assertJsonFragment(['email' => 'jane.smith@example.com']);
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'email' => 'jane.smith@example.com']);
    }

    public function test_destroy_deletes_employee()
    {
        $employee = Employee::factory()->create();

        $response = $this->deleteJson(route('api.employees.destroy', $employee));

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Employee deleted successfully']);
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_store_requires_required_fields()
    {
        $response = $this->postJson(route('api.employees.store'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'company_id']);
    }

    public function test_store_requires_unique_email()
    {
        $company = Company::factory()->create();
        Employee::factory()->create(['email' => 'taken@example.com', 'company_id' => $company->id]);

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'taken@example.com',
            'company_id' => $company->id,
        ];

        $response = $this->postJson(route('api.employees.store'), $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_requires_unique_email()
    {
        $company = Company::factory()->create();
        $employee1 = Employee::factory()->create(['email' => 'unique1@example.com', 'company_id' => $company->id]);
        $employee2 = Employee::factory()->create(['email' => 'unique2@example.com', 'company_id' => $company->id]);

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'unique1@example.com',
            'company_id' => $company->id,
        ];

        $response = $this->putJson(route('api.employees.update', $employee2), $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_allows_same_email_for_same_employee()
    {
        $company = Company::factory()->create();
        $employee = Employee::factory()->create(['email' => 'same@example.com', 'company_id' => $company->id]);

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'same@example.com',
            'company_id' => $company->id,
        ];

        $response = $this->putJson(route('api.employees.update', $employee), $data);
        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'same@example.com']);
    }

    protected function authenticate()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        return $user;
    }
}
