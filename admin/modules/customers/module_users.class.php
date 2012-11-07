<?

class Module_Users extends GW_Common_Module {

    function init() 
    {
        $this->model = new GW_User();
        $this->filters['id'] = GW::$request->path_arr[1]['data_object_id'];
        parent::init();
    }

    function viewDefault() 
    {
        $this->viewList();
    }

    function viewList() 
    {
        $list = $this->model->findAll('! removed');

        $this->smarty->assign('list', $list);
    }

    function doLogin() 
    {
        $keep_username = strtotime(GW::$static_conf['GW_LOGIN_NAME_EXPIRATION']);

        list($user, $pass) = $_POST['login'];
        setcookie('ulogin_0', $user, $keep_username, Navigator::getBase());

        //is request from dialog
        $dialog = basename(GW::$request->path) == 'dialog';


        if (!GW::$user = GW::$auth->loginPass($user, $pass)) {
            $this->setErrors(GW::$auth->error);
        } else {
            $this->smarty->assign('success', 1);
            $success = true;

            //autologin
            if ($_REQUEST['ulogin_auto'] && GW_Auth::isAutologinEnabled()) {
                setcookie('ulogin_7', GW::$user->getAutologinPass(), strtotime(GW::$static_conf['GW_AUTOLOGIN_EXPIRATION']), Navigator::getBase());
                GW::$auth->session['uautologin'] = 1;
            }
        }

        $ln = $_REQUEST['ln'] ? $_REQUEST['ln'] : Null;

        if ($ln)
            setcookie('login_ln', $ln, $keep_username, Navigator::getBase());

        if (!$dialog)
            GW::$request->jump('', Array('ln' => $ln));
    }

    function viewForm() 
    {

        $item = $this->model->createNewObject();
        if ($id = $this->filters['id']) {
            $item = $this->model->createNewObject($id);
            $item->load();
            $this->canBeAccessed($item, true);
        } elseif ($vals = $_REQUEST['item']) { // if error here will get values
            $item->setValues($vals);
        } elseif ($id = $_REQUEST['id']) { // edit existing
            $item = $this->model->createNewObject($id);

            $item->load();
        } else { // create new
        }

        $this->smarty->assign('item', $item);
    }


    function doSave() 
    {
        $vals = $_REQUEST['item'];

        $item = $this->model->createNewObject();

        if (!(int) $vals['id']) { // if insert	
            $item->setValidators('insert');
        } else { //if update
            $item->setValidators('update_info');
        }

        $item->setValues($vals);

        if (!$item->validate()) {
            $this->setErrors($item->errors);
            $this->processView('form');
            exit;
        }

        $item->setValidators(false); //remove validators
        $item->save();

        GW::$request->setMessage(GW::$lang['SAVE_SUCCESS']);
        $this->jumpAfterSave($item);
    }

    function doDelete() 
    {
        if (!$item = $this->getDataObjectById())
            return;

        if ($item->get('id') == GW::$user->get('id'))
            return $this->setErrors($this->lang['ERR_DELETE_SELF']);


        $item->delete();
        GW::$request->setMessage(GW::$lang['ITEM_REMOVE_SUCCESS']);

        $this->jump();
    }

    function doInvertActive() 
    {
        if (!$item = $this->getDataObjectById())
            return;

        if ($item->get('id') == GW::$user->get('id'))
            return $this->setErrors($this->lang['ERR_DEACTIVATE_SELF']);

        parent::doInvertActive();
    }

    function doInvertBanned() {
        if (!$item = $this->getDataObjectById())
            return;
        if ($item->get('id') == GW::$user->get('id'))
            return $this->setErrors($this->lang['ERR_DEACTIVATE_SELF']);

        if (!$item->invertBanned())
            return $this->setErrors('/GENERAL/ACTION_FAIL');

        $this->jump();
    }

}

?>
