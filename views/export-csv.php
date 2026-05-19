<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /finalProj/index.php");
    exit();
}

require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../controllers/product.php';
require_once __DIR__ . '/../public/database.config.php';

$controller = new ProductController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$products   = $controller->getAll();

// Set headers to trigger Excel file download
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="inventory_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Helper: escape XML special characters
function xesc($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
}
?>
<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:o="urn:schemas-microsoft-com:office:office">

  <Styles>

    <!-- Header style: neutral green background, bold, white text, border -->
    <Style ss:ID="header">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#2E7D32"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#2E7D32"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#2E7D32"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#2E7D32"/>
      </Borders>
      <Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF" ss:FontName="Calibri"/>
      <Interior ss:Color="#4CAF50" ss:Pattern="Solid"/>
    </Style>

    <!-- Data row style: light green tint, border, normal text -->
    <Style ss:ID="data">
      <Alignment ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
      </Borders>
      <Font ss:Size="10" ss:Color="#1A1A1A" ss:FontName="Calibri"/>
      <Interior ss:Color="#F1F8E9" ss:Pattern="Solid"/>
    </Style>

    <!-- Alternating data row: slightly darker tint -->
    <Style ss:ID="data_alt">
      <Alignment ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
      </Borders>
      <Font ss:Size="10" ss:Color="#1A1A1A" ss:FontName="Calibri"/>
      <Interior ss:Color="#DCEDC8" ss:Pattern="Solid"/>
    </Style>

    <!-- Currency cell -->
    <Style ss:ID="currency">
      <Alignment ss:Vertical="Center" ss:Horizontal="Right"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
      </Borders>
      <Font ss:Size="10" ss:Color="#1A1A1A" ss:FontName="Calibri"/>
      <Interior ss:Color="#F1F8E9" ss:Pattern="Solid"/>
      <NumberFormat ss:Format="#,##0.00"/>
    </Style>

    <!-- Currency alt -->
    <Style ss:ID="currency_alt">
      <Alignment ss:Vertical="Center" ss:Horizontal="Right"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
      </Borders>
      <Font ss:Size="10" ss:Color="#1A1A1A" ss:FontName="Calibri"/>
      <Interior ss:Color="#DCEDC8" ss:Pattern="Solid"/>
      <NumberFormat ss:Format="#,##0.00"/>
    </Style>

    <!-- In Stock badge -->
    <Style ss:ID="in_stock">
      <Alignment ss:Vertical="Center" ss:Horizontal="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A5D6A7"/>
      </Borders>
      <Font ss:Size="10" ss:Bold="1" ss:Color="#1B5E20" ss:FontName="Calibri"/>
      <Interior ss:Color="#C8E6C9" ss:Pattern="Solid"/>
    </Style>

    <!-- Out of Stock badge -->
    <Style ss:ID="out_stock">
      <Alignment ss:Vertical="Center" ss:Horizontal="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF9A9A"/>
        <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF9A9A"/>
        <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF9A9A"/>
        <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF9A9A"/>
      </Borders>
      <Font ss:Size="10" ss:Bold="1" ss:Color="#B71C1C" ss:FontName="Calibri"/>
      <Interior ss:Color="#FFCDD2" ss:Pattern="Solid"/>
    </Style>

    <!-- Title style -->
    <Style ss:ID="title">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:Bold="1" ss:Size="14" ss:Color="#1B5E20" ss:FontName="Calibri"/>
      <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
    </Style>

    <!-- Subtitle style -->
    <Style ss:ID="subtitle">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:Size="10" ss:Color="#757575" ss:FontName="Calibri"/>
      <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
    </Style>

  </Styles>

  <Worksheet ss:Name="Inventory Report">
    <Table ss:DefaultRowHeight="18">

      <!-- Column widths -->
      <Column ss:Width="45"/>   <!-- ID -->
      <Column ss:Width="160"/>  <!-- Name -->
      <Column ss:Width="280"/>  <!-- Description -->
      <Column ss:Width="100"/>  <!-- Price -->
      <Column ss:Width="80"/>   <!-- Quantity -->
      <Column ss:Width="100"/>  <!-- Status -->
      <Column ss:Width="100"/>  <!-- Date Added -->

      <!-- Title Row -->
      <Row ss:Height="28">
        <Cell ss:StyleID="title"><Data ss:Type="String">Inventory Management System — Export Report</Data></Cell>
      </Row>

      <!-- Subtitle Row -->
      <Row ss:Height="18">
        <Cell ss:StyleID="subtitle"><Data ss:Type="String">Generated on: <?= date('F d, Y \a\t h:i A') ?></Data></Cell>
      </Row>

      <!-- Spacer Row -->
      <Row ss:Height="10"/>

      <!-- Header Row -->
      <Row ss:Height="22">
        <Cell ss:StyleID="header"><Data ss:Type="String">ID</Data></Cell>
        <Cell ss:StyleID="header"><Data ss:Type="String">Product Name</Data></Cell>
        <Cell ss:StyleID="header"><Data ss:Type="String">Description</Data></Cell>
        <Cell ss:StyleID="header"><Data ss:Type="String">Price (PHP)</Data></Cell>
        <Cell ss:StyleID="header"><Data ss:Type="String">Quantity</Data></Cell>
        <Cell ss:StyleID="header"><Data ss:Type="String">Status</Data></Cell>
        <Cell ss:StyleID="header"><Data ss:Type="String">Date Added</Data></Cell>
      </Row>

<?php
$row_index = 0;
foreach ($products as $product):
    $is_alt    = ($row_index % 2 !== 0);
    $data_style     = $is_alt ? 'data_alt'     : 'data';
    $currency_style = $is_alt ? 'currency_alt' : 'currency';
    $status         = ((int)$product['quantity'] > 0) ? 'In Stock' : 'Out of Stock';
    $status_style   = ((int)$product['quantity'] > 0) ? 'in_stock' : 'out_stock';
    $date           = date('Y-m-d', strtotime($product['created_at']));
    $row_index++;
?>
      <Row ss:Height="18">
        <Cell ss:StyleID="<?= $data_style ?>"><Data ss:Type="Number"><?= (int)$product['id'] ?></Data></Cell>
        <Cell ss:StyleID="<?= $data_style ?>"><Data ss:Type="String"><?= xesc($product['name']) ?></Data></Cell>
        <Cell ss:StyleID="<?= $data_style ?>"><Data ss:Type="String"><?= xesc($product['description']) ?></Data></Cell>
        <Cell ss:StyleID="<?= $currency_style ?>"><Data ss:Type="Number"><?= (float)$product['price'] ?></Data></Cell>
        <Cell ss:StyleID="<?= $data_style ?>"><Data ss:Type="Number"><?= (int)$product['quantity'] ?></Data></Cell>
        <Cell ss:StyleID="<?= $status_style ?>"><Data ss:Type="String"><?= $status ?></Data></Cell>
        <Cell ss:StyleID="<?= $data_style ?>"><Data ss:Type="String"><?= $date ?></Data></Cell>
      </Row>
<?php endforeach; ?>

    </Table>
  </Worksheet>
</Workbook>
