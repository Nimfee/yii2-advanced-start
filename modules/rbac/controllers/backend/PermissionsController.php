<?php

namespace modules\rbac\controllers\backend;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use modules\rbac\models\Permission;
use modules\rbac\Module;

/**
 * Class PermissionsController
 * @package modules\rbac\controllers\backend
 */
class PermissionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['managerRbac'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Permission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $auth->getPermissions(),
            'sort' => [
                'attributes' => ['name', 'description', 'ruleName'],
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Permission model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($id);

        $model = new Permission(['name' => $permission->name]);
        return $this->render('view', [
            'permission' => $permission,
            'model' => $model,
        ]);
    }

    /**
     * Creates Permission a new Permission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Permission(['scenario' => Permission::SCENARIO_CREATE]);
        $model->isNewRecord = true;
        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $perm = $auth->createPermission($model->name);
            $perm->description = $model->description;
            if ($auth->add($perm)) {
                return $this->redirect(['view', 'id' => $model->name]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Permission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $auth = Yii::$app->authManager;
        $perm = $auth->getPermission($id);

        $model = new Permission([
            'scenario' => Permission::SCENARIO_UPDATE,
            'name' => $perm->name,
            'description' => $perm->description,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $perm->description = $model->description;
            if ($auth->update($id, $perm)) {
                return $this->redirect(['view', 'id' => $id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Привязываем разрешение
     * @return array|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionAddPermissions()
    {
        $model = new Permission([
            'scenario' => Permission::SCENARIO_UPDATE,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $permission = $auth->getPermission($model->name);
            foreach ($model->permissionItems as $perm) {
                $add = $auth->getPermission($perm);
                $auth->addChild($permission, $add);
            }
            return $this->redirect(['update', 'id' => $model->name, '#' => 'assign-container-permissions']);
        }
        throw new BadRequestHttpException(Module::t('module', 'Not a valid request to the method!'));
    }

    /**
     * Отвязываем разрешение
     * @return array|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionRemovePermissions()
    {
        $model = new Permission([
            'scenario' => Permission::SCENARIO_UPDATE,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $permission = $auth->getPermission($model->name);
            foreach ($model->permissions as $perm) {
                $remove = $auth->getPermission($perm);
                $auth->removeChild($permission, $remove);
            }
            return $this->redirect(['update', 'id' => $model->name, '#' => 'assign-container-permissions']);
        }
        throw new BadRequestHttpException(Module::t('module', 'Not a valid request to the method!'));
    }

    /**
     * Deletes an existing Permission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $auth = Yii::$app->authManager;
        $perm = $auth->getPermission($id);
        $auth->remove($perm);
        return $this->redirect(['index']);
    }
}
