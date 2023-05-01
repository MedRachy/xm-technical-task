<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>XM Technical task</title>
    {{-- bootstrap css --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    {{-- jQueryui --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-5">XM technical task </h1>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            <div class="col-12">
                <form method="POST" action="{{ route('companies.handelSubmittedForm') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="companySymbol" class="form-label">Company Symbol</label>
                        <select id="companySymbol" name="companySymbol"
                            class="form-select @error('companySymbol') is-invalid @enderror">
                            @foreach ($symbols as $symbol)
                                <option value="{{ $symbol }}" @if (old('companySymbol') != '' && old('companySymbol') == $symbol) selected @endif>
                                    {{ $symbol }}
                                </option>
                            @endforeach
                        </select>
                        {{-- the @error directive is used for server-side validation  --}}
                        {{-- jquery is used for client-side validation  --}}
                        <span id="companySymbolError" class="invalid-feedback" role="alert">
                            @error('companySymbol')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </span>

                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input id="startDate" name="startDate" placeholder="YYYY-mm-dd" value="{{ old('startDate') }}"
                            class="form-control  @error('startDate') is-invalid @enderror">
                        {{-- the @error directive is used for server-side validation  --}}
                        {{-- jquery is used for client-side validation  --}}
                        <span id="startDateError" class="invalid-feedback" role="alert">
                            @error('startDate')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </span>

                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input id="endDate" name="endDate" placeholder="YYYY-mm-dd" value="{{ old('endDate') }}"
                            class="form-control @error('endDate') is-invalid @enderror">
                        {{-- the @error directive is used for server-side validation  --}}
                        {{-- jquery is used for client-side validation  --}}
                        <span id="endDateError" class="invalid-feedback" role="alert">
                            @error('endDate')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </span>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror">
                        {{-- the @error directive is used for server-side validation  --}}
                        {{-- jquery is used for client-side validation  --}}
                        <span id="emailError" class="invalid-feedback" role="alert">
                            @error('email')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    {{-- Bootstrap js --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    {{-- jQuery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"
        integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- jQueryui --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {

            // init datepicker 
            $("#startDate , #endDate").datepicker({
                dateFormat: "yy-mm-dd",
                maxDate: 0 // set maximum date to today
            });

            // Form valdiation (front-side)
            $('form').on('submit', function(event) {

                event.preventDefault();
                let isValid = true;
                // inputs
                const companySymbolInput = $('#companySymbol');
                const startDateInput = $('#startDate');
                const endDateInput = $('#endDate');
                const emailInput = $('#email');
                // errors
                const companySymbolError = $('#companySymbolError');
                const startDateError = $('#startDateError');
                const endDateError = $('#endDateError');
                const emailError = $('#emailError');

                const startDate = new Date(startDateInput.val());
                const endDate = new Date(endDateInput.val());
                const email = emailInput.val().trim();

                // Validate CompanySymbol : required
                if (companySymbolInput.val() === '') {
                    companySymbolInput.addClass('is-invalid');
                    companySymbolError.text('The company symbol field is required.')
                        .css('font-weight', 'bolder');
                    companySymbolError.show();
                    isValid = false;
                }
                // Validate CompanySymbol : valid string
                else if (typeof companySymbolInput.val() !== "string") {
                    companySymbolInput.addClass('is-invalid');
                    companySymbolError.text('The company symbol is not a valid string.')
                        .css('font-weight', 'bolder');
                    companySymbolError.show();
                    isValid = false;
                } else {
                    companySymbolInput.removeClass('is-invalid');
                    companySymbolError.text('');
                }

                // Validate Start Date : required
                if (startDateInput.val() == '') {
                    startDateInput.addClass('is-invalid');
                    startDateError.text('The start date field is required.')
                        .css('font-weight', 'bolder');
                    startDateError.show();
                    isValid = false;
                }
                // Validate Start Date : format , year , month and day
                else if (!validateDate(startDateInput.val())) {
                    startDateInput.addClass('is-invalid');
                    startDateError.text('The start date is not a valid date.')
                        .css('font-weight', 'bolder');
                    startDateError.show();
                    isValid = false;
                }
                // Validate Start Date : must be a date before or equal to today
                else if (startDate > new Date()) {
                    startDateInput.addClass('is-invalid');
                    startDateError.text('Start Date must be a date before or equal to today.')
                        .css('font-weight', 'bolder');
                    startDateError.show();
                    isValid = false;
                } else {
                    startDateInput.removeClass('is-invalid');
                    startDateError.text('');
                }

                //  Validate End Date : required
                if (endDateInput.val() == '') {
                    endDateInput.addClass('is-invalid');
                    endDateError.text('The end date field is required.')
                        .css('font-weight', 'bolder');
                    endDateError.show();
                    isValid = false;
                }
                // Validate End Date : format , year , month and day
                else if (!validateDate(endDateInput.val())) {
                    endDateInput.addClass('is-invalid');
                    endDateError.text('The end date is not a valid date.')
                        .css('font-weight', 'bolder');
                    endDateError.show();
                    isValid = false;
                }
                // Validate End Date : must be a date after or equal to start date
                else if (endDate < startDate) {
                    endDateInput.addClass('is-invalid');
                    endDateError.text('End date must be a date after or equal to start date.')
                        .css('font-weight', 'bolder');
                    endDateError.show();
                    isValid = false;
                }
                // Validate End Date : must be a date before or equal to today
                else if (endDate > new Date()) {
                    endDateInput.addClass('is-invalid');
                    endDateError.text('End Date must be a date before or equal to today.')
                        .css('font-weight', 'bolder');
                    endDateError.show();
                    isValid = false;
                } else {
                    endDateInput.removeClass('is-invalid');
                    endDateError.text('');
                }

                // Validate Email : required
                if (email === '') {
                    emailInput.addClass('is-invalid');
                    emailError.text('The email field is required.').css('font-weight', 'bolder');
                    emailError.show();
                    isValid = false;
                }
                // Validate Email : email format
                else if (!/\S+@\S+\.\S+/.test(email)) {
                    emailInput.addClass('is-invalid');
                    emailError.text('The email must be a valid email address.')
                        .css('font-weight', 'bolder');
                    emailError.show();
                    isValid = false;
                } else {
                    emailInput.removeClass('is-invalid');
                    emailError.text('');
                }

                // submit form if all data validated 
                if (isValid) {
                    this.submit();
                }
            });

            // Date format validation
            function validateDate(inputDate) {

                var isDateValid = true;

                // validate format using expression : yyyy-mm-dd
                var dateRegex = /^(\d{4})-(\d{2})-(\d{2})$/;
                var isFormatValid = dateRegex.test(inputDate);

                // convert input date to Date object
                var selectedDate = new Date(inputDate);

                // validate date 
                if (isFormatValid && (selectedDate.toString() != 'Invalid Date')) {
                    // validate year
                    if (selectedDate.getFullYear() < 1900) {
                        isDateValid = false;
                    } else {
                        // validate month
                        if (selectedDate.getMonth() < 0 || selectedDate.getMonth() > 11) {
                            isDateValid = false;
                        } else {
                            // validate day
                            if (selectedDate.getDate() < 1 || selectedDate.getDate() > 31) {
                                isDateValid = false;
                            }
                        }
                    }
                } else {
                    isDateValid = false;
                }
                return isDateValid;
            }
        });
    </script>
</body>

</html>
