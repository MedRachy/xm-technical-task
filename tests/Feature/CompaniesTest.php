<?php

namespace Tests\Feature;

use App\Mail\SendEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompaniesTest extends TestCase
{
    /**
     * test page rendering.
     *
     * @return void
     */
    public function test_can_render_index_view_with_data()
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertViewIs('index')
            ->assertViewHas('symbols');
    }

    public function test_pass_validation_rules_for_company_symbol()
    {
        // test rule : required 
        $invalidData = [
            'companySymbol' => '',
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => 'validemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors(['companySymbol' => 'The company symbol field is required.']);

        // test rule : invalid symbol  
        $invalidData = [
            'companySymbol' => 123,
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => 'validemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors(['companySymbol' => 'The company symbol must be a string.']);
    }

    public function test_pass_validation_rules_for_start_date_and_end_date()
    {
        // test rule : required 
        $invalidData = [
            'companySymbol' => 'AAL',
            'startDate' => '',
            'endDate' => '',
            'email' => 'validemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors([
                'startDate' => 'The start date field is required.',
                'endDate' => 'The end date field is required.'
            ]);

        // test rules : 
        //  * Start date less or equal than End Date, and less or equal than current date 
        //  * End date greater or equal than Start Date, and less or equal than current date
        $invalidData = [
            'companySymbol' => 'AAL',
            'startDate' => '2024-08-08',
            'endDate' => '2024-05-05',
            'email' => 'validemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'startDate' => 'The start date must be a date before or equal to end date.',
            'startDate' => 'The start date must be a date before or equal to today.',
            'endDate' => 'The end date must be a date after or equal to start date.',
            'endDate' => 'The end date must be a date before or equal to today.'
        ]);

        // test rules : valid date format 
        $invalidData = [
            'companySymbol' => 'AAL',
            'startDate' => '2023-13-01',
            'endDate' => '2023-04-33',
            'email' => 'validemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'startDate' => 'The start date is not a valid date.',
            'endDate' => 'The end date is not a valid date.'
        ]);
    }

    public function test_pass_validation_rules_for_email()
    {
        // test rule : required 
        $invalidData = [
            'companySymbol' => 'AAL',
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => ''
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors(['email' => 'The email field is required.']);

        // test rule : valid email 
        $invalidData = [
            'companySymbol' => 'AAL',
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => 'unvalid email'
        ];

        $response = $this->post('/companies/historical-quotes', $invalidData);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors(['email' => 'The email must be a valid email address.']);
    }

    public function test_render_historical_quotes_view_with_valid_data()
    {
        //  valid parameters 
        $ValidData = [
            'companySymbol' => 'AAL',
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => 'testemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $ValidData);

        $response->assertOk()
            ->assertViewIs('historical_quotes')
            ->assertViewHas('quotes')
            ->assertViewHas('companySymbol', $ValidData['companySymbol']);
    }

    public function test_display_historical_quotes_in_the_given_date_range()
    {
        //  valid parameters 
        $ValidData = [
            'companySymbol' => 'AAL',
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => 'testemail@email.com'
        ];

        $response = $this->post('/companies/historical-quotes', $ValidData);
        $quotes = $response->original->getData()['quotes'];
        $count = count($quotes);

        $response->assertOk();

        // assert that quotes are between startDate and endDate  
        \PHPUnit\Framework\TestCase::assertGreaterThanOrEqual($ValidData['startDate'], $quotes[$count - 1]['date']);
        \PHPUnit\Framework\TestCase::assertLessThanOrEqual($ValidData['endDate'], $quotes[0]['date']);
    }

    public function test_email_sent_after_form_submitted()
    {
        Mail::fake();
        //  valid parameters 
        $ValidData = [
            'companyName' => 'American Airlines Group, Inc.',
            'companySymbol' => 'AAL',
            'startDate' => '2023-04-01',
            'endDate' => '2023-04-29',
            'email' => 'testemail@email.com'
        ];

        $this->post('/companies/historical-quotes', $ValidData);

        // test that an email was sent to email submitted  
        Mail::assertSent(SendEmail::class, function ($mail) use ($ValidData) {
            return $mail->hasTo($ValidData['email']);
        });
    }
}
