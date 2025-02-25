<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attribute Listing</title>
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
        .save-btn {
            margin-top: 10px;
            padding: 10px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            float: right;
            margin-bottom: 20px;
        }
        .save-btn:hover {
            background: #45a049;
        }
    </style>
    
</head>
<body>
    <h2>Attribute Listing</h2>
    <form id="attribute_review_form">
    @csrf
    <table>
        <thead>
            <tr>
                <th>Attribute</th>
                <th>Questions</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentAttributeType = null;
                $attributeQuestionsCount = [];
                foreach ($data as $row) {
                    $attributeQuestionsCount[$row->attribute_id] = 
                        isset($attributeQuestionsCount[$row->attribute_id]) 
                        ? $attributeQuestionsCount[$row->attribute_id] + 1 
                        : 1;
                }
                $printedAttributes = [];
            @endphp

            @foreach ($data as $row)
                @if ($currentAttributeType !== $row->attribute_type_id)
                    <!-- Display Attribute Type Header -->
                    <tr>
                        <td colspan="3" style="background: #d3d3d3; font-weight: bold;">
                            {{ $row->attribute_type ?? 'N/A' }}
                        </td>
                    </tr>
                    @php $currentAttributeType = $row->attribute_type_id; @endphp
                @endif

                <tr>
                    @if (!isset($printedAttributes[$row->attribute_id]))
                        <!-- Merge rows for the same Attribute Name -->
                        <td rowspan="{{ $attributeQuestionsCount[$row->attribute_id] }}" style="background: #f0f0f0; font-weight: bold;">
                            {{ $row->attribute_name ?? 'N/A' }}
                        </td>
                        @php $printedAttributes[$row->attribute_id] = true; @endphp
                    @endif
                    <td>{{ $row->attribute_question ?? 'N/A' }}</td>
                    <td>
                        <select name="rating[{{ $row->attribute_question_id }}]">
                            <option value="">Select</option>
                            <option value="{{$row->attribute_id}}-1">1 - Poor</option>
                            <option value="{{$row->attribute_id}}-2">2 - Fair</option>
                            <option value="{{$row->attribute_id}}-3">3 - Good</option>
                            <option value="{{$row->attribute_id}}-4">4 - Very Good</option>
                            <option value="{{$row->attribute_id}}-5">5 - Excellent</option>
                        </select>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="button" class="save-btn">Save Ratings</button>
    </form>


    <script>
        $(document).ready(function () {
            $(".save-btn").on("click", function () {
                debugger;
                let formData = new FormData($("#attribute_review_form")[0]); // Get all form data

                // $(".rating").each(function () {
                //     let questionId = $(this).data("id");
                //     let ratingValue = $(this).val();
                //     if (ratingValue) {
                //         formData.append(`ratings[${questionId}]`, ratingValue);
                //     }
                // });

                

                $.ajax({
                    url: "/saveRatings",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false, 
                    success: function (response) {
                        $(".message").fadeIn().delay(2000).fadeOut();
                    },
                    error: function () {
                        alert("Something went wrong!");
                    }
                });
            });
        });
    </script>
</body>
</html>
