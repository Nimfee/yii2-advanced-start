<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use modules\users\widgets\passfield\Passfield;
use modules\users\models\backend\User;
use modules\users\Module;

/* @var $this yii\web\View */
/* @var $model modules\users\models\backend\User */
/* @var $uploadModel modules\users\models\UploadForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-backend-default-update">
    <?php
    $model->scenario = $model::SCENARIO_PASSWORD_UPDATE;
    $form = ActiveForm::begin([
        'action' => Url::to(['update-password', 'id' => $model->id]),
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-2',
                'offset' => 'col-sm-offset-2',
                'wrapper' => 'col-sm-10',
            ],
        ],
    ]); ?>

    <?= Passfield::widget([
        'form' => $form,
        'model' => $model,
        'attribute' => 'newPassword',
        'options' => [
            'maxlength' => true,
            'class' => 'form-control',
            'placeholder' => Module::t('backend', 'New Password'),
        ],
        'config' => [
            'locale' => mb_substr(Yii::$app->language, 0, strrpos(Yii::$app->language, '-')),
            'showToggle' => true,
            'showGenerate' => true,
            'showWarn' => true,
            'showTip' => true,
            'length' => [
                'min' => User::LENGTH_STRING_PASSWORD_MIN,
                'max' => User::LENGTH_STRING_PASSWORD_MAX,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'newPasswordRepeat')->passwordInput([
        'maxlength' => true,
        'class' => 'form-control',
        'placeholder' => Module::t('backend', 'Repeat Password'),
    ]) ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? '<span class="fa fa-plus"></span> ' . Module::t('backend', 'Create') : '<span class="fa fa-floppy-o"></span> ' . Module::t('backend', 'Update'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'name' => 'submit-button',
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>