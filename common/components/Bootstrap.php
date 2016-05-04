<?php
namespace common\components;

use yii\base\BootstrapInterface;
use yii\helpers\Url;

class Bootstrap implements BootstrapInterface
{
	public function bootstrap($app)
	{
		$app->params['imagePath'] = $app->basePath.'/web/photos/';
		$app->params['imageUrl'] = Url::to('@web/photos/', true);
		$app->params['x12resource'] = $app->basePath.'/x12resource';
	}
}

?>