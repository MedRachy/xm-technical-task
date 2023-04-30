<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>XM Technical task</title>
    {{-- bootstrap css --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mt-5">XM technical task </h1>
            </div>
            <div class="col-12">
                <h3 class="my-3">Historical quotes for company : {{ $companySymbol }}</h3>
                <a href="{{ route('home') }}">Go back</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Open</th>
                            <th scope="col">High</th>
                            <th scope="col">Close</th>
                            <th scope="col">Low</th>
                            <th scope="col">Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($quotes)
                            @foreach ($quotes as $quote)
                                <tr>
                                    <th scope="row">{{ $quote['date'] }}</th>
                                    <td>{{ $quote['open'] }}</td>
                                    <td>{{ $quote['high'] }}</td>
                                    <td>{{ $quote['close'] }}</td>
                                    <td>{{ $quote['low'] }}</td>
                                    <td>{{ $quote['volume'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <div class="text-center text-warning my-3 ">No historical quotes for this period
                            </div>

                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="my-3">Chart :</h3>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-area me-1"></i>
                        Open and close prices between : {{ $startDate }} and {{ $endDate }}
                    </div>
                    <div class="card-body"><canvas id="openCloseChart" width="100%" height="30"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    {{-- jQuery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"
        integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- Chart js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"
        integrity="sha512-TW5s0IT/IppJtu76UbysrBH9Hy/5X41OTAbQuffZFU6lQ1rdcLHzpU5BzVvr/YFykoiMYZVWlr/PX1mDcfM9Qg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            // get chart data 
            const quotes = @json($quotes);
            var openPricesData = {};
            var closePricesData = {};

            // create two datasets for open and close prices 
            $.each(quotes, function(index, item) {
                openPricesData[item.date] = item.open;
                closePricesData[item.date] = item.close;
            });

            // chart configs
            const configbar = {
                type: 'bar',
                data: {
                    datasets: [{
                            label: 'Open',
                            backgroundColor: 'rgb(255, 99, 132)',
                            data: openPricesData
                        },
                        {
                            label: 'Close',
                            backgroundColor: 'rgb(255, 193, 7)',
                            data: closePricesData
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            // init charts
            const openCloseChart = new Chart(document.getElementById('openCloseChart'), configbar);

        })
    </script>
</body>

</html>
