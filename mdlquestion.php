<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Modal</title>
    <!-- Add Bootstrap CSS for styling and modal functionality -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- <style>
    /* Custom CSS to style the modal */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-footer {
        background-color: #f1f1f1;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .close {
        color: white;
        opacity: 1;
    }

    .close:hover {
        color: #ccc;
    }
    </style> -->
</head>

<body>
    <div class="modal" tabindex="-1" role="dialog" id="mdlQuestion">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Modal title
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Câu hỏi</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="1"
                            placeholder="Nhập câu hỏi"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add jQuery and Bootstrap JS for modal functionality -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>