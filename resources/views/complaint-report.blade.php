<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 mt-5">

<div class="container mx-auto bg-white p-8 rounded-lg shadow-md">
    <h4 class="text-xl font-semibold mb-4">Start Proper Waste Disposal Today</h4>
    <h2 class="text-5xl font-bold mb-4">Complaint Report</h2>
    <p class="mb-5">Please fill out the complaint report form for the waste management system to ensure your concerns are addressed promptly.</p>

    <form>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="firstName" placeholder="">
            </div>
            <div class="form-group">
                <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="lastName" placeholder="">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="form-group">
                <label for="phoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="phoneNumber" placeholder="">
            </div>
            <div class="form-group">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="email" placeholder="">
            </div>
        </div>

        <div class="form-group mt-4">
            <label for="resident-id" class="block text-sm font-medium text-gray-700">Resident ID Number</label>
            <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="resident-id" placeholder="">
        </div>
        
        <div class="form-group mt-4">
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="address" placeholder="">
        </div>

        <div class="form-group mt-4">
            <label for="attachments" class="block text-sm font-medium text-gray-700">Evidence Attachments</label>
            <input type="file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="attachments" multiple>
            <small class="text-gray-500">You can attach multiple files.</small>
        </div>

        <div class="form-group mt-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" id="message" rows="4" placeholder="Write your message..."></textarea>
        </div>

        <button type="submit" class="mt-4 bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600">Submit</button>
    </form>
</div>

</body>
</html>