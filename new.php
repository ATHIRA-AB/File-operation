<?php
class PDFUploader
{
    private $con;

    public function __construct($dbcon)
    {
        $this->con = $dbcon;
    }

    public function uploadPDF($name, $file)
    {
        if (isset($file['name'])) {
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            if ($this->isPDF($file_name)) {
                $destination = "./pdf/" . $file_name;
                if (move_uploaded_file($file_tmp, $destination)) {
                    $insertquery = "INSERT INTO pdf_data(username, filename) VALUES('$name', '$file_name')";
                    $iquery = mysqli_query($this->con, $insertquery);

                    if ($iquery) {
                        return "success";
                    } else {
                        return "failed";
                    }
                } else {
                    return "upload_failed";
                }
            } else {
                return "invalid_format";
            }
        }
    }

    public function getAllRecords()
    {
        $selectQuery = "SELECT * FROM pdf_data";
        $squery = mysqli_query($this->con, $selectQuery);
        $records = [];

        while ($result = mysqli_fetch_assoc($squery)) {
            $records[] = $result;
        }

        return $records;
    }

    private function isPDF($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return strtolower($extension) == "pdf";
    }
}

include 'dbcon.php';

$pdfUploader = new PDFUploader($con);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $file = $_FILES['pdf_file'];

    $uploadResult = $pdfUploader->uploadPDF($name, $file);

    switch ($uploadResult) {
        case "success":
            $message = "Data submitted successfully.";
            $alertClass = "alert-success";
            break;
        case "failed":
            $message = "Failed! Try Again!";
            $alertClass = "alert-danger";
            break;
        case "upload_failed":
            $message = "Failed! File upload failed.";
            $alertClass = "alert-danger";
            break;
        case "invalid_format":
            $message = "Failed! File must be uploaded in PDF format.";
            $alertClass = "alert-danger";
            break;
    }
}

$records = $pdfUploader->getAllRecords();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="margin-top:30px">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-12">
            <strong>Fill UserName and Upload PDF</strong>
            <form method="post" enctype="multipart/form-data">
                <?php if (isset($message)): ?>
                    <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show text-center">
                        <a class="close" data-dismiss="alert" aria-label="close">×</a>
                        <strong><?php echo $message; ?></strong>
                    </div>
                <?php endif; ?>

                <div class="form-input py-2">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Enter your name" name="name">
                    </div>
                    <div class="form-group">
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf" required/>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btnRegister" name="submit" value="Submit">
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Records from Database</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                            <th>ID</th>
                            <th>UserName</th>
                            <th>FileName</th>
                            </thead>
                            <tbody>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo $record['id']; ?></td>
                                    <td><?php echo $record['username']; ?></td>
                                    <td><?php echo $record['filename']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
