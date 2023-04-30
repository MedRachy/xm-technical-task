<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CompanyController extends Controller
{

    public function index()
    {
        // get data from the cache if its exists, else return new api response  
        $companiesData = Cache::remember('companiesData', Carbon::now()->addDays(1), function () {
            return $this->getCompaniesApiData();
        });
        // map companies data to get only symbols 
        $symbols = $companiesData->map(function ($item) {
            return $item['Symbol'];
        });

        return view('index', ['symbols' => $symbols]);
    }

    public function handelSubmittedForm(Request $request)
    {
        // validate inputs 
        $request->validate([
            'companySymbol' => ['required', 'string'],
            'startDate' => ['required', 'date', 'before_or_equal:endDate', 'before_or_equal:today'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate', 'before_or_equal:today'],
            'email' => ['required', 'email'],
        ]);

        // parameters 
        $companySymbol = $request->companySymbol;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $email = $request->email;

        // 1 - Checks if the cache exists for the given set of parameters, 
        // 2 - If the cache exists return the cached data, else make new api call
        // 3 - filter data if exists and save in cache   
        $cacheKey = "api-data-" . $companySymbol . "-" . $startDate . "-" . $endDate;
        $quotes = Cache::remember($cacheKey, Carbon::now()->addDays(1), function () use ($companySymbol, $startDate, $endDate) {
            $unfiltredApiData = $this->getApiData($companySymbol);
            return (isset($unfiltredApiData) ?  $this->filterByDateRange($unfiltredApiData, $startDate, $endDate) : null);
        });
        /* --------------
         * Send Email 
         * NOTE : To avoid response delay : SendEmail class should implement "ShouldQueue"
         * so it can be processed async when running queue:work (after config QUEUE_CONNECTION with database driver for example and running migration)  
         * but it was not asked in the exercie,
         * and im not sur how the exercie will be tested     
         * --------------
        */
        $companyName = $this->getCompanyName($companySymbol);
        Mail::to($email)->send(new SendEmail($companySymbol, $companyName, $startDate, $endDate));

        return view('historical_quotes', [
            'quotes' => $quotes,
            'companySymbol' => $companySymbol,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    private function getCompaniesApiData()
    {
        try {

            $response = Http::get('https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json');

            if ($response->ok()) {
                return $response->collect();
            }
            /* --------------
            * NOTE : i coulnd found this api documentation online, for more response errors handling ! 
            * --------------
            */
        } catch (RequestException $e) {

            $response = $e->response;
            if ($response->clientError()) {
                // handle client errors (4xx status codes)
                return back()->withErrors(['message' => 'The API endpoint returned a client error.']);
            } elseif ($response->serverError()) {
                // handle server errors (5xx status codes)
                return back()->withErrors(['message' => 'The API endpoint returned a server error.']);
            } else {
                // handle other exceptions
                return back()->withErrors(['message' => 'An error occurred while calling the API endpoint.']);
            }
        }
    }

    private function getCompanyName($symbol)
    {
        // get data from the cache if its exists, else return new api response  
        $companiesData = Cache::remember('companiesData', Carbon::now()->addDays(1), function () {
            return $this->getCompaniesApiData();
        });
        $company = $companiesData->firstWhere('Symbol', $symbol);

        return (isset($company) ? $company['Company Name'] : '');
    }

    private function getApiData($companySymbol)
    {
        try {

            $response = Http::withHeaders([
                'X-RapidAPI-Key' => 'a9e67e8dffmsh90255b6d94d3291p1b03c0jsn617b8e4e0345',
                'X-RapidAPI-Host' => 'yh-finance.p.rapidapi.com'
            ])->get('https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data', [
                'symbol' => $companySymbol,
                'region' => 'US',
            ]);

            if ($response->ok()) {

                $data = $response->collect();
                $unfiltredData = collect($data['prices']);

                return $unfiltredData;
            }
            /* --------------
            * NOTE : i coulnd found this api documentation online, for more response errors handling ! 
            * --------------
            */
        } catch (RequestException $e) {

            $response = $e->response;
            if ($response->clientError()) {
                // handle client errors (4xx status codes)
                return back()->withErrors(['message' => 'The API endpoint returned a client error.']);
            } elseif ($response->serverError()) {
                // handle server errors (5xx status codes)
                return back()->withErrors(['message' => 'The API endpoint returned a server error.']);
            } else {
                // handle other exceptions
                return back()->withErrors(['message' => 'An error occurred while calling the API endpoint.']);
            }
        }
    }

    private function filterByDateRange($unfiltredApiData, $startDate, $endDate)
    {
        // filter by date range : start date - end date 
        $filtredApiData = $unfiltredApiData->filter(function ($item) use ($startDate, $endDate) {
            // parse the date using Carbon
            $date = Carbon::parse($item['date'])->format('Y-m-d');
            // keeping only quotes between start date and end date
            return $date >= $startDate && $date <= $endDate;
        })->map(function ($item) {
            // parse the date using Carbon
            $item['date'] = Carbon::parse($item['date'])->format('Y-m-d');
            return $item;
        });

        return $filtredApiData;
    }
}
