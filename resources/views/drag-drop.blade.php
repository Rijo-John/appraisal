<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag & Drop Questions</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
  .container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    width: 100%;
    height: 100vh;
    padding: 20px;
}

.left-panel, .right-panel {
    height: 312px;
    width: 48%;
    border: 1px solid #ccc;
    padding: 10px;
    background: #f9f9f9;
    overflow-y: auto;
}

.question-item {
    padding: 10px;
    background: #e3e3e3;
    margin-bottom: 5px;
    border: 1px solid #999;
    cursor: grab;
    max-width: 100%;
    word-wrap: break-word;
}

.designation-area {
    min-height: 310px;
    background: #dff0d8;
    border: 2px dashed #4CAF50;
    padding: 10px;
}


    </style>
</head>
<body>

    <h2>Select Designation</h2>
    <select id="designationDropdown">
        <option value="">Select Designation</option>
        @foreach ($designations as $designation)
            <option value="{{ $designation->id }}">{{ $designation->designation_name }}</option>
        @endforeach
    </select>
    <button id="saveDataBtn">Save Selection</button>
    <div class="container">
    <!-- Left Side: Questions List -->
    <div class="left-panel">
        <h2>Questions</h2>
        <div class="question-list">
            @foreach ($questions as $question)
                <div class="question-item" data-id="{{ $question->id }}">
                    {{ $question->question }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Side: Drop Here Section -->
    <div class="right-panel">
        <h2>Drop Here</h2>
        <div class="designation-area"></div>
        
    </div>
   
</div>



    <script>
   $(function() {
    $(".question-item").draggable({
        revert: "invalid",
        helper: function(event) {
            var clone = $(this).clone();
            clone.css({
                "max-width": "250px",
                "white-space": "normal",
                "background": "#f1f1f1",
                "padding": "10px",
                "border": "1px solid #ccc"
            });
            return clone;
        }
    });

    $(".designation-area").droppable({
        accept: ".question-item",
        drop: function(event, ui) {
            var questionId = $(ui.draggable).data('id');

            // Remove the original from the question list
            $(ui.draggable).remove();

            // Clone the dragged item and add a delete button
            var clonedElement = $(ui.draggable).clone();
            clonedElement.append('<button class="remove-btn" data-id="' + questionId + '">❌</button>');

            $(this).append(clonedElement);
        }
    });

    // Move question back to original list when ❌ is clicked
    $(document).on("click", ".remove-btn", function() {
        var questionElement = $(this).closest(".question-item");
        
        // Remove the ❌ button
        $(this).remove();
        
        // Append back to the question list
        $(".question-list").append(questionElement);
        
        // Reinitialize draggable
        questionElement.draggable({
            revert: "invalid",
            helper: function(event) {
                var clone = $(this).clone();
                clone.css({
                    "max-width": "250px",
                    "white-space": "normal",
                    "background": "#f1f1f1",
                    "padding": "10px",
                    "border": "1px solid #ccc"
                });
                return clone;
            }
        });
    });


    // Save Data to Database
    $("#saveDataBtn").click(function() {
        debugger;
        var selectedDesignation = $("#designationDropdown").val();
        var questionIds = [];

        // Get all dropped question IDs
        $(".designation-area .question-item").each(function() {
            questionIds.push($(this).data("id"));
        });

        if (!selectedDesignation) {
            alert("Please select a designation.");
            return;
        }

        if (questionIds.length === 0) {
            alert("No questions assigned!");
            return;
        }

        // Send data via AJAX to Laravel
        $.ajax({
            url: "/save-designation-questions", 
            method: "POST",
            data: {
                designation_id: selectedDesignation,
                question_ids: questionIds,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                alert(response.message);
            },
            error: function(error) {
                console.error("Error:", error);
            }
        });
    });





});



</script>

</body>
</html>
