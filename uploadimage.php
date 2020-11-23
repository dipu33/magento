<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);
use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
$fileCsv = $objectManager->get('\Magento\Framework\File\Csv');
$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
$baseUrl = $storeManager->getStore()
    ->getBaseUrl();
$urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
$response = $objectManager->get('\Magento\Framework\App\ResponseInterface');
$messageManager = $objectManager->create('\Magento\Framework\Message\ManagerInterface');

$state->setAreaCode('frontend');
$rootPath = $directory->getRoot();
$file = $rootPath . '/images.csv';
if (file_exists($file))
{
    $data = $fileCsv->getData($file);

    unset($data[0]);
    foreach ($data as $FileData)
    {
        $proSku = $FileData[0];
        $proImageUrl = $FileData[1];
        $imgData = $FileData[2];

        $imagePath = $rootPath . '/pub/media/' . $imgData;
        file_put_contents($imagePath, file_get_contents($proImageUrl));
        $imageType = array(
            'image',
            'small_image',
            'thumbnail'
        ); /*For all images attributes*/
        $product = $objectManager->get('Magento\Catalog\Model\Product')
            ->loadByAttribute('sku', $proSku);

        $product->addImageToMediaGallery($imagePath, $imageType, false, false);
        $product->save();

        echo "Your Product Sku's -> " . $proSku . " Image is uploaded" . "<br/>";

    }

}

?>