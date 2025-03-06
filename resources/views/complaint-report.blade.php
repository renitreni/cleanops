<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 mt-5">

    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md">
        <h4 class="text-xl font-semibold mb-4">Waste & Damage Management Solution</h4>
        <h2 class="text-5xl font-bold mb-4">Complaint Report</h2>
        <p class="mb-5">Please fill out the complaint report form for the waste management system to ensure your
            concerns are addressed promptly.</p>

        <form method="POST" action="{{ route('complaint-report.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="form-group">
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Fullname</label>
                    <input type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('fullname') border-red-500 @enderror"
                        id="fullname" name="fullname" value="{{ old('first_name') }}">
                    @error('fullname')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('phone_number') border-red-500 @enderror"
                        id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('email') border-red-500 @enderror"
                        id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hidden geolocation fields -->
            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

            <div class="mt-2 mb-4">
                <div id="locationStatus" class="text-sm">
                    <span class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Getting your location...
                    </span>
                </div>
            </div>

            <div class="form-group mt-4">
                <label for="attachment1" class="block text-sm font-medium text-gray-700">Evidence Attachments</label>
                <input type="file"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('attachment1') border-red-500 @enderror"
                    id="attachment1" name="attachment1">
                @error('attachment1')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mt-4">
                <label for="attachment2" class="block text-sm font-medium text-gray-700">Evidence Attachments</label>
                <input type="file"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('attachment2') border-red-500 @enderror"
                    id="attachment2" name="attachment2">
                @error('attachment2')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mt-4">
                <label for="attachment3" class="block text-sm font-medium text-gray-700">Evidence Attachments</label>
                <input type="file"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('attachment3') border-red-500 @enderror"
                    id="attachment3" name="attachment3">
                @error('attachment3')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mt-4">
                <label for="attachment4" class="block text-sm font-medium text-gray-700">Evidence Attachments</label>
                <input type="file"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('attachment4') border-red-500 @enderror"
                    id="attachment4" name="attachment4">
                @error('attachment4')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mt-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 @error('message') border-red-500 @enderror"
                    id="message" name="message" rows="4">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="mt-4 bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600">Submit</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const locationStatus = document.getElementById('locationStatus');
            const submitButton = document.querySelector('button[type="submit"]');

            // Initially disable submit button until location is obtained
            submitButton.disabled = false;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');

            // Try to get location automatically when page loads
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Success - store coordinates in hidden fields
                        latitudeInput.value = position.coords.latitude;
                        longitudeInput.value = position.coords.longitude;

                        // Update status message with success
                        locationStatus.innerHTML = '<span class="text-green-500 flex items-center">' +
                            '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' +
                            'Location obtained successfully</span>';

                        // Enable submit button
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    },
                    function(error) {
                        // Error handling
                        console.error('Error getting location:', error);
                        let errorMessage = 'Could not get your location. ';

                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Please enable location permissions in your browser settings.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Location information is unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'The request to get your location timed out.';
                                break;
                            default:
                                errorMessage += 'An unknown error occurred.';
                        }

                        // Update status with error message
                        locationStatus.innerHTML = '<span class="text-red-500 flex items-center">' +
                            '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' +
                            errorMessage + '</span>';

                        // Still enable the button to allow manual submission
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                // Browser doesn't support geolocation
                locationStatus.innerHTML = '<span class="text-red-500 flex items-center">' +
                    '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' +
                    'Geolocation is not supported by your browser.</span>';

                // Enable the button anyway
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
        @if ($message = session('succes_message'))
            Swal.fire(
                'Complaint has been sent!',
                '{{ $message }}',
                'success'
            )
        @endif
    </script>

</body>

</html>
