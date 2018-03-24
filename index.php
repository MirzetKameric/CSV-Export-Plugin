<?php

/**
 * Plugin Name:       CSV Export
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       Plugin to export all emails to a CSV file
 * Version:           1.0.0
 * Author:            Kameric Mirzet
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       csv-export
 * Domain Path:       /languages
 */
class CSVExport {

  /**
   * Constructor
   */
  public function __construct() {
    if (isset($_GET['report'])) {

      $csv = $this->generate_csv();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private", false);
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"report.csv\";");
      header("Content-Transfer-Encoding: binary");

      echo $csv;
      exit;
    }

// Add extra menu items for admins
    add_action('admin_menu', array($this, 'admin_menu'));

// Create end-points
    add_filter('query_vars', array($this, 'query_vars'));
    add_action('parse_request', array($this, 'parse_request'));
  }

  /**
   * Add extra menu items for admins
   */
  public function admin_menu() {
    add_menu_page('Download CSV', 'Download CSV', 'manage_options', 'download_csv', array($this, 'download_csv'));
  }

  /**
   * Allow for custom query variables
   */
  public function query_vars($query_vars) {
    $query_vars[] = 'download_csv';
    return $query_vars;
  }

  /**
   * Parse the request
   */
  public function parse_request(&$wp) {
    if (array_key_exists('download_csv', $wp->query_vars)) {
      $this->download_csv();
      exit;
    }
  }

  /**
   * Download report
   */
   public function download_csv()
   {
   echo '<div class="wrap">';
   echo '<div id="icon-tools" class="icon32">
   </div>';
   echo '<h2>Download Subscribers to CSV File</h2>';

   echo '<p><a href="?page=download_csv&report=users">Export the Subscribers</a></p>';

   echo '<table class="widefat">';
   echo '<thead>';
   echo '<tr >';
   echo '<th class="manage-column column-cb" scope="col" style="width: 30px; text-align: center;">ID</th>';
   echo '<th class="manage-column column-columnname">Email</th>';
   echo '</tr>';
   echo '</thead>';

   global $wpdb;
   $result = $wpdb->get_results ( "SELECT * FROM myTable" );
   foreach ( $result as $print )   {
     echo '<tr>';
     echo '<td style="width: 30px; text-align: center;">';
     echo $print->id;
     echo '</td>';
     echo '<td>';
     echo $print->emails;
     echo '</td>';
     echo '</tr>';
    }
   }

  /**
   * Converting data to CSV
   */
  public function generate_csv() {
    $csv_output = '';
    $table = 'myTable';

    $con=mysqli_connect("localhost","root","root","vdw");

   $result = mysqli_query($con,"SHOW COLUMNS FROM " . $table . "");

    $i = 0;
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $csv_output = $csv_output . $row['Field'] . ",";
        $i++;
      }
    }
    $csv_output .= "\n";

    $values = mysqli_query($con, "SELECT * FROM " . $table . "");
    while ($rowr = mysqli_fetch_row($values)) {
      for ($j = 0; $j < $i; $j++) {
        $csv_output .= $rowr[$j] . ",";
      }
      $csv_output .= "\n";
    }

    return $csv_output;
  }

}

// Instantiate a singleton of this plugin
$csvExport = new CSVExport();
