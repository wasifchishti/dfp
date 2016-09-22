<?php


$path = dirname(__FILE__) . '/../lib';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

echo "testing feature branch";

require_once 'Google/Api/Ads/Dfp/Util/v201605/StatementBuilder.php';
require_once 'ExampleUtils.php';

function getLineItemsDFP($user, $orderId) {

  $lineItemService = $user->GetService('LineItemService', 'v201605');
  $lineItems = array();

  // Create a statement to select all line items.
  $statementBuilder = new StatementBuilder();
  $statementBuilder->Where("orderId = :orderId")
    ->OrderBy('id ASC')
    ->Limit(StatementBuilder::SUGGESTED_PAGE_LIMIT)
    ->WithBindVariableValue('orderId', $orderId);

  // Default for total result set size.
  $totalResultSetSize = 0;

  do {
    // Get line items by statement.
    $page = $lineItemService->getLineItemsByStatement(
      $statementBuilder->ToStatement());

    // Display results.
    if (isset($page->results)) {
      $totalResultSetSize = $page->totalResultSetSize;

      foreach ($page->results as $lineItem) {
        $lineItems[] = $lineItem;
      }
    }

    $statementBuilder->IncreaseOffsetBy(StatementBuilder::SUGGESTED_PAGE_LIMIT);

  } while ($statementBuilder->GetOffset() < $totalResultSetSize);

  return $lineItems;
}



function getLineItemById($user, $id) {

  $lineItemService = $user->GetService('LineItemService', 'v201605');

  // Create a statement to select the line item.
  $statementBuilder = new StatementBuilder();
  $statementBuilder->Where("id = :lineItemId")
    ->OrderBy('id ASC')
    ->Limit(1)
    ->WithBindVariableValue('lineItemId', $id);

  $page = $lineItemService->getLineItemsByStatement($statementBuilder->ToStatement());

  return $page->results[0];
}


function getLineItemsCSV($file) {

  $results = array();
  $data = array();

  // Read Excel file
  $handle = fopen('files/' . $file, 'r');

  if (!$handle) {
    die("Unable to open CSV file.\n");
  }

  $i = 0;
  while ($data = fgetcsv($handle)) {
    if ($i > 0) {
      $results[] =  $data[3];
    }

    $i++;
  }

  return $results;
}
