<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appraisal Form Listing</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h2>Appraisal Form Listing</h2>
    
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Employee Code</th>
                <th>Finalized</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $row)
                <tr>
                    <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                    <td>{{ $row->emp_code }}</td>
                    <td>{{ $row->appraiser_finalise == 1 ? 'Yes' : 'No' }}</td>
                    <td><a href="{{ route('your.route.name', ['id' => $row->first_name]) }}">View Details</a>
</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
