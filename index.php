<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    private $yfxq;
    private $jsnl;
    private $zrzl;
    private $userInfo;
    private $news;
    private $cgzhinfo;
    public function _initialize(){
        $this->yfxq = M("yfxqinfo");
        $this->jsnl = M("jsnlinfo");
        $this->zrzl = M("zrzlinfo");
        $this->news = M("news");
        $this->userinfo = M('userinfo');
        $this->cgzhinfo = M("cgzhinfo");


        //最新10资讯
        $this->hot10();

        parent::_initialize();
    }
    public function index(){
       //专利信息
       $this->zrzl1 = $this->zrzl->order("id desc")->where(array("zlhyfl"=>"新能源","status"=>"通过"))->limit(6)->select();
       $this->zrzl2 = $this->zrzl->order("id desc")->where(array("zlhyfl"=>"新材料","status"=>"通过"))->limit(6)->select();
       $this->zrzl3 = $this->zrzl->order("id desc")->where(array("zlhyfl"=>"生物医药","status"=>"通过"))->limit(6)->select();
       $this->zrzl4 = $this->zrzl->order("id desc")->where(array("zlhyfl"=>"高端制造","status"=>"通过"))->limit(6)->select();
       $this->zrzl5 = $this->zrzl->order("id desc")->where(array("zlhyfl"=>"其他","status"=>"通过"))->limit(6)->select();

        //研发需求
        $this->yfxq1 = $this->yfxq->order("id desc")->where(array("HYLB"=>"新能源","status"=>"通过"))->limit(6)->select();
        $this->yfxq2 = $this->yfxq->order("id desc")->where(array("HYLB"=>"新材料","status"=>"通过"))->limit(6)->select();
        $this->yfxq3 = $this->yfxq->order("id desc")->where(array("HYLB"=>"生物医药","status"=>"通过"))->limit(6)->select();
        $this->yfxq4 = $this->yfxq->order("id desc")->where(array("HYLB"=>"高端制造","status"=>"通过"))->limit(6)->select();
        $this->yfxq5 = $this->yfxq->order("id desc")->where(array("HYLB"=>"其他","status"=>"通过"))->limit(6)->select();

        //最新资讯
        $where['recylebin'] = array('neq',1);
        $this->news7 = $this->news->order("id desc")->where($where)->limit(7)->select();

        //图片滚动
        $where['recylebin'] = array('eq',1);
        $this->slide4 = $this->news->order('id desc')->where($where)->limit(4)->select();

        // 成果转展示
        $this->cg2 = $this->cgzhinfo->order("id desc")->limit(4)->select();

        //高端服务
        $this->assign("service","");

        //产业专区
        $this->assign("industry","");

        //技术能力
        $this->jsnl1 = $this->jsnl->order("id desc")->where(array("HYLB"=>"新能源","status"=>"通过"))->limit(6)->select();
        $this->jsnl2 = $this->jsnl->order("id desc")->where(array("HYLB"=>"新材料","status"=>"通过"))->limit(6)->select();
        $this->jsnl3 = $this->jsnl->order("id desc")->where(array("HYLB"=>"生物医药","status"=>"通过"))->limit(6)->select();
        $this->jsnl4 = $this->jsnl->order("id desc")->where(array("HYLB"=>"高端制造","status"=>"通过"))->limit(6)->select();
        $this->jsnl5 = $this->jsnl->order("id desc")->where(array("HYLB"=>"其他","status"=>"通过"))->limit(6)->select();

        $this->display();
    }

    /*列表页*/
    // 专利转让
    public function lzrzl(){
        $type = I('get.type');
        if(!empty($type)){
            $fl = C("FENG_LEI");
            $where['zlhyfl'] = $fl[$type];
            $where['status'] = "通过";
            $this->assign("curType",$type);

        }else{
            $where['status'] = "通过";
        }


        $this->plist($this->zrzl,$where);
        $this->display();
    }
    // 研发需求
    public function lyfxq(){
        $type = I('get.type');
        if(!empty($type)){
            $fl = C("FENG_LEI");
            $where['HYLB'] = $fl[$type];
            $where['status'] = "通过";
            $this->assign("curType",$type);

        }else{
            $where = '';
        }
        $this->plist($this->yfxq,$where);
        $this->display();
    }
    //技术能力
    public function ljsnl(){
        $type = I('get.type');
        if(!empty($type)){
            $fl = C("FENG_LEI");
            $where['HYLB'] = $fl[$type];
            $where['status'] = "通过";
            $this->assign("curType",$type);

        }else{
            $where = '';
        }
        $this->plist($this->jsnl,$where);
        $this->display();
    }

    private function plist($model,$where=''){
        $count = $model->where($where)->count();
        $page = new \Think\Page($count,10);
        $page->setConfig("prev","上一页");
        $page->setConfig('next',"下一页");
        $show = $page->show();

        $list = $model->order('ID desc')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->assign("count",$count);
    }

    //下载附件
    public function download(){
        $str = I('get.t');
        $id = I('get.id');

        if($str == 'advzl'){
            $field = 'pdfaddress';
            $model = M("chinapatent");
        }else{
            $model = M($str.'info');
        }
        if($str == 'zrzl'){
            $field = 'pdfaddress';
        }
        if($str == 'jsnl'){
            $field = 'WJ';
        }
        if($str == 'yfxq'){
            $field = 'XQWJ';
        }

        $file = $model->where(array('ID'=>$id))->field($field)->find();


        if(!$file[strtolower($field)]){
            $this->error("数据不存在！");
        }

        $file = $file[strtolower($field)];
        $ext = end(explode('.', $file));

        $file_dir = "D:/$file";

        if(file_exists($file_dir)){
            $file = fopen($file_dir,"r");
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: ".filesize($file_dir));
            Header("Content-Disposition: attachment; filename=" . time().".".$ext);
            echo fread($file,filesize($file_dir));
            fclose($file);
            exit;
        }else{
            $this->error("文件不存在！");
        }

    }


    //详情页
    public function dyfxq(){
        $id = I("get.id",0,"intval");
        $this->ddetail($this->yfxq,$id);
        $this->display();
    }

    public function djsnl(){
        $id = I("get.id",0,"intval");
        $this->ddetail($this->jsnl,$id);
        $this->display();
    }

    public function dzrzl(){
        $id = I("get.id",0,"intval");
        $this->ddetail($this->zrzl,$id);
        $this->display();
    }

    private function ddetail($model,$id){
        $data = $model->where(array("ID"=>$id,"status"=>"通过"))->find();
        $this->assign("data",$data);

    }
    // 搜索页面
    public function search(){
        $type = I('get.stype','','trim');
        $keyword =I('get.keyword','','trim');

        if($type == '' || $keyword == ''){
            $this->redirect('Home/Index/index');
        }

        if($type == 'zrzl'){
            $model = $this->zrzl;
            $where['_string'] = '(Inventionname like "%'.$keyword.'%") OR (abstractInfo like "%'.$keyword.'%")';
            $where['status'] = "通过";
        }

        if($type == 'jsnl'){
            $model = $this->jsnl;
            $where['status'] = "通过";
            $where['JSNLJJ'] = array('like','%'.$keyword.'%');
        }

        if($type == 'yfxq'){
            $model = $this->yfxq;
            // $where = "`XQJJ` like '%".$keyword."%' ";
            $where['status'] = "通过";
            $where['XQjj'] = array('like','%'.$keyword.'%');
        }
        $count = $model->where($where)->count();
        $page = new \Think\Page($count,10);
        $page->setConfig("prev","上一页");
        $page->setConfig('next',"下一页");
        $show = $page->show();

        $list = $model->order('ID desc')->where($where)->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('count',$count);
        $this->assign('list', $list);
        $this->stype = $type;
        $this->keyword = $keyword;
        $this->assign('page',$show);

        $this->display();
    }
    //用户登录操作
    public function login(){
        if($_POST){
            $UserID = trim(I('post.UserID'));
            $pwd = md5(trim(I('post.pwd')));
            $user = M('userinfo');
            $where = array('UserID'=>$UserID,'pwd'=>$pwd);
            $result = $user->where($where)->field('UserID')->find();
            if($result){
                session('uid',$result['userid']);
                $this->redirect("/home/Index/index");
            }else{
                $this->error("用户名或密码不正确!",U('Home/Index/index'),3);
            }
        }else{

            $this->display("login");
        }
    }
    // 用户注册
    public function register(){
        if($_POST){
            $user = M("userinfo");
            $UserID = trim(I("post.UserID"));
            $pwd = MD5(trim(I('post.pwd')));
            $YHLX = trim(I("post.YHLX"));
            if($user->add(array("UserID"=>$UserID,"YHLX"=>$YHLX,"pwd"=>$pwd))){
                session('uid',$UserID);
                $this->redirect("/home/Index/index");
            }
        }else{
            $this->display();
        }
    }
    //退出系统
    public function loginout(){
        session(null);
        cookie(null);
        $this->redirect('/Home/Index/index');
    }
    //难用户是否已注册过
    public function validID(){
        $user = M("userinfo");
        $uid = trim(I("post.param"));
        $rs = $user->where(array("UserID"=>$uid))->find();

        if($rs){
            echo json_encode(array("info"=>'该用户被注册了',"status"=>"n"));
        }else{
            echo json_encode(array("info"=>'此用户名可以注册',"status"=>"y"));
        }
    }


    //行业资讯
    //列表页
    public function newslist(){
        $this->rightinfo();
        $this->plist($this->news);
        $this->display();
    }
    //详情页
    public function dnews(){
        $this->rightinfo();
        $id = I('get.id',0,'intval');
        $where['id'] = array('eq',$id);
        $datas = $this->news->where($where)->find();
        $this->assign("datas",$datas);
        $this->display();
    }

    private function rightinfo(){
        $zrzl10 = $this->zrzl->order("ID desc")->limit(5)->select();
        $this->assign("zrzl10",$zrzl10);
    }


    // 专利查询
    public function advsearch(){
        $model = M('chinapatent');
        $type = I("get.type","","trim");
        $keyword = I("get.keyword","","trim");

        if($type == 'normal'){
            $where['applicationnum'] = array("like","%".$keyword."%");
            $where['inventionname'] = array("like","%".$keyword."%");
            $where['abstractInfo'] = array("like","%".$keyword."%");
            $where['_logic'] = 'OR';
        }

        if($type == 'adv'){
            $keywords = array();
            if(I('ksqh')!=''){
                $where['applicationnum'] = array("like","%".I('ksqh')."%");
                $keywords[] = I("ksqh");
            }
            if(I('kzlsqrq')!=''){
                $where['applicationdate'] = array("like","%".I('kzlsqrq')."%");
                $keywords[] = I("kzlsqrq");
            }
            if(I('kzlgkr')!=''){
                $where['publicationnum'] = array("like","%".I('kzlgkr')."%");
                $keywords[] = I("kzlgkr");
            }
            if(I('kzlmc')!=''){
                $where['inventionname'] = array("like","%".I('kzlmc')."%");
                $keywords[] = I("kzlmc");
            }
            if(I('kipc')!=''){
                $where['ipc'] = array("like","%".I('kipc')."%");
                $keywords[] = I("kipc");
            }
            if(I('kzlzy')!=''){
                $where['abstractInfo'] = array("like","%".I('kzlzy')."%");
                $keywords[] = I("kzlzy");
            }
            $where['_logic'] = "OR";

        }

        $count = $model->where($where)->count();

        $page = new \Think\Page($count,15);
        $page->setConfig("prev","上一页");
        $page->setConfig('next',"下一页");
        $show = $page->show();

        $list = $model->where($where)->limit($page->firstRow.','.$page->listRows)->select();

        if($type == 'normal'){
            $this->assign("keyword",$keyword);

        }

        if($type == 'adv'){
            $this->keyword = implode($keywords,",");
        }

        $this->assign("page",$show);
        $this->assign("count",$count);
        $this->assign("list",$list);

        $this->display();
    }

    /*成果转化展示*/
    function cgzhlist(){
        $this->rightinfo();
        $this->plist($this->cgzhinfo);
        $this->display();
    }

    /*成果转化展示详情*/
    public function dcgzh(){
        $id = I("get.id",0,'intval');
        if(!$id) {$this->redirect('index');}

        $datas = $this->cgzhinfo->where(array('id'=>$id))->find();

        $this->assign("datas",$datas);

        $this->display();

    }


    public function advdetail(){
        $model = M('chinapatent');
        $applicationnum = I('get.id');

        $where['applicationnum'] = array("eq",$applicationnum);

        $data = $model->where($where)->find();

        $this->assign("data",$data);
        $this->display();
    }

    private function hot10(){
        $hot10 = $this->news->order('id desc')->limit()->select();
        $this->assign('hot10',$hot10);

    }
}