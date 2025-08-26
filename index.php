<?php
include 'DBconnect.php';  // connect to DB

?>
<!DOCTYPE html>
<html>
<head>
  <title>User List</title>
  <style>
    table { border-collapse: collapse; width: 50%; }
    th, td { border: 1px solid black; padding: 8px; text-align: left; }
  </style>
</head>
<body>
  <h1>User Table</h1>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
    </tr>
    <?php
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["User_ID"] . "</td>
                    <td>" . $row["Name"] . "</td>
                    <td>" . $row["Email"] . "</td>
                    <td>" . $row["Phone"] . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No users found</td></tr>";
    }
    $conn->close(); // close the connection
    ?>
  </table>
</body>
</html>
