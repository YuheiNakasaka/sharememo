<?php

class UserMainController extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        //auto authorized
        //$this->beforeFilter('user',array('except' => array('show')));
    }

    /**
     * Memo top
     * @return index
     */
    public function index()
    {
        if (!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        //hash value of auto save
        $data['asdfid']    = uniqid(rand(1000,9999));

        return $this->_display('main/index', $data);
    }

    /**
     * Create memo
     * @return create
     */
    public function create(){
        if (!Auth::check()) return Redirect::to('/');
        //get Input
        $input = Input::only(array('top_aisatsu_title','top_aisatsu_text','sharestatus','_asdfid'));
        //Validation
        $rules = array(
            'top_aisatsu_title'=>'max:199|required',
            'top_aisatsu_text' =>'max:40000|required'
            );
        $validator = Validator::make($input,$rules);
        //if validation succeed
        if (!$validator->fails()) {
            //set data to create new record
            $data['user_id'] = Auth::user()->id;
            $data['title']   = $input['top_aisatsu_title'];
            $data['title']   = preg_replace("/<script>.*<\/script>/",'',$data['title']);
            $data['title']   = preg_replace('/<iframe.*\s*\n*\r*>/','',$data['title']);
            $data['memo']    = $input['top_aisatsu_text'];
            //generate unique url
            $data['urlpath'] = uniqid(rand(10,99));
            //public or private
            $data['status']  = intval($input['sharestatus']);
            //insert data
            Memo::create($data);
            //delete a draft logicaly
            $draft = Draft::where('draft_id',$input['_asdfid'])->where('user_id',$data['user_id'])->first();
            if (count($draft) != 0) {
                $draft->status = 1;
                $draft->save();
            }

            return Redirect::to('/memo/' . $data['urlpath']);
        } else {
        //Invalid data
            $data              = array();
            $data['base_path'] = '/app/sharememo/app/views/';
            $data['auth']      = Auth::check();
            $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
            $data['errors']    = $validator->messages()->all();
            $data['asdfid']    = $input['_asdfid'];
            return $this->_display('main/index',$data);
        }
    }

    /**
     * Show a memo
     * @param  string $id
     * @return        show
     */
    public function show($id)
    {
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';

        try {
            $record = Memo::where('urlpath',$id)->firstOrFail();

            //0 is public.1 is private.
            if ($record['status'] == 1 && $data['auth'] === false) return Redirect::to('404');
            //memo lists at sidebar
            $user_memo_lists         = Memo::where('user_id',$record['user_id'])->where('status', 0)->orderBy('created_at','desc')->take(5)->get();
            $user_memo               = User::where('id',$record['user_id'])->first();
            $data['write_user_name'] = $user_memo['screen_name'];
        } catch (Exception $e) {
            return Redirect::to('/404');
        }
        $data['id']              = $id;
        $data['memo']            = preg_replace("/<script>.*<\/script>/",'',$record['memo']);
        //よくわからないことしてる
        $data['memo']            = preg_match('/<iframe.*src="\/\/www\.youtube\.com\/.+"\s.+>.*<\/iframe>/',$data['memo'],$m) == true?$data['memo']:preg_replace('/<\s*\r*\n*i\s*\r*\n*f\s*\r*\n*r\s*\r*\n*a\s*\r*\n*m\s*\r*\n*e.*\s*\r*\n*>.*\s*\r*\n*<\s*\r*\n*\/\s*\r*\n*i\s*\r*\n*f\s*\r*\n*r\s*\r*\n*a\s*\r*\n*m\s*\r*\n*e\s*\r*\n*>/','',$data['memo']);
        $data['title']           = $record['title'];
        $data['created_at']      = $record['created_at'];
        $data['user_id']         = $record['user_id'];
        $data['provider']        = $user_memo['oauth_token'];
        $data['memo_image_url']  = $data['provider'] == 'facebook'?'https://graph.facebook.com/' . $data["user_id"] . '/picture':'http://twiticon.herokuapp.com/' . $data["write_user_name"] . '/mini';
        $data['user_url']        = $data['provider'] == 'facebook'?'https://www.facebook.com/'.$data['user_id']:'http://twitter.com/'.$data['write_user_name'];
        $data['user_memo_lists'] = $user_memo_lists;

        //if owner, be able to edit the memo
        if (!is_null(Auth::user())) {
            $data['owner'] = $record['user_id'] == Auth::user()->id?$record['user_id']:'';
        }
        return $this->_display('main/show',$data);
    }

    /**
     * Edit a memo
     * @param  string $id
     * @return edit
     */
    public function edit($id)
    {
        if(!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        //hash value of auto save
        $data['asdfid']    = uniqid(rand(1000,9999));

        //get a record to match with $id
        $record = Memo::where('urlpath', $id)->first();

        $data['id']     = $id;
        $data['memo']   = $record['memo'];
        $data['title']  = $record['title'];
        $data['status'] = $record['status'];

        return $this->_display('main/edit',$data);
    }

    /**
     * Update a memo
     * @param  string $id
     * @return update
     */
    public function update($id)
    {
        if (!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        $data['user_id']   = $data['auth']?Auth::user()->id:'';
        //get Input
        $input = Input::only(array('top_aisatsu_title','top_aisatsu_text','sharestatus','_asdfid'));
        //Validation
        $rules = array(
            'top_aisatsu_title'=>'max:199|required',
            'top_aisatsu_text'=>'max:40000|required'
            );
        $validator = Validator::make($input,$rules);
        //if validation succeed
        if (!$validator->fails()) {
            $memo         = Memo::where('urlpath',$id)->firstOrFail();
            $memo->title  = $input['top_aisatsu_title'];
            $memo->memo   = $input['top_aisatsu_text'];
            $memo->status = intval($input['sharestatus']);
            $memo->save();

            //delete a draft logicaly
            $draft = Draft::where('draft_id',$input['_asdfid'])->where('user_id',$data['user_id'])->first();
            if (count($draft) != 0) {
                $draft->status = 1;
                $draft->save();
            }

            //set parameter to display title,memo,author.
            $data['id']    = $id;
            $data['memo']  = $memo['memo'];
            $data['title'] = $memo['title'];

            //if owner, be able to edit the memo
            if(!is_null(Auth::user())){
                $data['owner'] = $memo['user_id'] == Auth::user()->id?$memo['user_id']:'';
            }
            return Redirect::action('UserMainController@show', array($id));
        }else{
        //Invalid data
            $data = array();
            $data['base_path'] = '/app/sharememo/app/views/';
            $data['auth']      = Auth::check();
            $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
            $data['errors']    = $validator->messages()->all();
            $data['asdfid']    = $input['_asdfid'];
            return $this->_display('main/index',$data);
        }
    }

    /**
     * Delete a memo
     * @param  string $id
     * @return index
     */
    public function delete($id)
    {
        if (!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';

        //delete a record
        $memo = Memo::where('urlpath',$id)->first();
        $memo->delete();

        $data['id']    = $id;
        $data['memo']  = $memo['memo'];
        $data['title'] = $memo['title'];

        return Redirect::to('/memo');
    }

    /**
     * Display user's memos
     * @return notes
     */
    public function notes($id = '')
    {
        if (!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';

        //guests are not allowed to read status 1 memos.
        if ($data['auth']) {
            if ($id == '') {
                $data['memos'] = Memo::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(10);
            } else {
                $data['memos'] = Memo::where('user_id',$id)->orderBy('created_at','desc')->paginate(10);
            }
        } else {
            $record = User::where('id', $id)->first();

            //fi not registered user,redirect to top
            if (empty($record)) return Redirect::to('/');
            //get memo data
            $data['memos'] = Memo::where('user_id',$id)->where('status', 0)->orderBy('created_at','desc')->paginate(10);

            $data['user_id']        = $id;
            $data['user_name']      = $record['screen_name'];
            $data['provider']       = $record['oauth_token'];
            $data['memo_image_url'] = $data['provider'] == 'facebook'?'https://graph.facebook.com/' . $id . '/picture':'http://twiticon.herokuapp.com/' . $data["user_name"] . '/mini';
            $data['user_url']       = $data['provider'] == 'facebook'?'https://www.facebook.com/'.$id:'http://twitter.com/'.$data['user_name'];
        }

        return $this->_display('main/notes',$data);
    }

    /**
     * Search for user's memos
     * @return notes
     */
    public function searchnotes()
    {
        if(!Auth::check()) return Redirect::to('/');
        //get Input
        $input      = Input::only(array('q'));
        $input['q'] = htmlspecialchars($input['q'],ENT_QUOTES,'UTF-8');

        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        $data['q']         = $input['q'];

        $searchword    = '%' . $input['q'] . '%';
        $data['memos'] = Memo::where('user_id',Auth::user()->id)->where('memo','like',$searchword)->orderBy('created_at','desc')->paginate(10);

        return $this->_display('main/notes',$data);
    }

    /**
     * Display user's drafts
     * @return notes
     */
    public function drafts()
    {
        if(!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        $data['drafts']    = Draft::where('user_id',Auth::user()->id)->where('status',0)->orderBy('created_at','desc')->paginate(10);
        return $this->_display('main/drafts',$data);
    }

    /**
     * Edit a draft
     * @param  string $id
     * @return draft
     */
    public function editdraft($id)
    {
        if(!Auth::check()) return Redirect::to('/');
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        $data['user_id']   = $data['auth']?Auth::user()->id:'';
        //hash value of auto save
        $data['asdfid']    = $id;

        //get a record to match with $id
        $record = Draft::where('draft_id', $id)->where('user_id', $data['user_id'])->first();

        $data['id']    = $id;
        $data['memo']  = $record['memo'];
        $data['title'] = $record['title'];

        return $this->_display('main/index',$data);
    }

    /**
     * Delete a draft
     * @param  string $id
     * @return index
     */
    public function deletedraft($id)
    {
        if(!Auth::check()) return json_encode(array('status' => 'error'));
        $data              = array();
        $data['base_path'] = '/app/sharememo/app/views/';
        $data['auth']      = Auth::check();
        $data['user_name'] = $data['auth']?Auth::user()->screen_name:'';
        $data['user_id']   = Auth::check()?Auth::user()->id:'';

        //delete a record
        $draft = Draft::where('draft_id',$id)->where('user_id',$data['user_id'])->first();
        $draft->delete();

        return json_encode(array('status' => 'success'));
    }

    /**
     * Auto save
     * @return json
     */
    public function autosave()
    {
        if (!Auth::check()) return json_encode(array('status' => 'error'));
        //get Input
        $input = Input::only(array('top_aisatsu_title', 'top_aisatsu_text', '_asdfid'));
        //Validation
        $rules = array(
            'top_aisatsu_title'=>'max:199|required',
            'top_aisatsu_text'=>'max:40000|required'
            );
        $validator = Validator::make($input,$rules);
        //if validation succeed
        if (!$validator->fails()) {
            //set data to create new record
            $data['user_id']  = Auth::check()?Auth::user()->id:'';
            $data['title']    = $input['top_aisatsu_title'];
            $data['title']    = preg_replace("/<script>.*<\/script>/",'',$data['title']);
            $data['title']    = preg_replace('/<iframe.*\s*\n*\r*>/','',$data['title']);
            $data['memo']     = preg_replace("/<script>.*<\/script>/",'',$input['top_aisatsu_text']);
            //よくわからないことしてる
            $data['memo']     = preg_match('/<iframe.*src="\/\/www\.youtube\.com\/.+"\s.+>.*<\/iframe>/',$data['memo'],$m) == true?$data['memo']:preg_replace('/<\s*\r*\n*i\s*\r*\n*f\s*\r*\n*r\s*\r*\n*a\s*\r*\n*m\s*\r*\n*e.*\s*\r*\n*>.*\s*\r*\n*<\s*\r*\n*\/\s*\r*\n*i\s*\r*\n*f\s*\r*\n*r\s*\r*\n*a\s*\r*\n*m\s*\r*\n*e\s*\r*\n*>/','',$data['memo']);
            //unique draft id
            $data['draft_id'] = $input['_asdfid'];

            //if asdfid is not existed, create new record, otherwise update the record.
            $draft = Draft::where('draft_id', $data['draft_id'])->where('user_id', $data['user_id'])->first();

            if ($draft['exists'] !== true) {
                //create data
                Draft::create($data);
                return json_encode(array('status' => 'success'));
            } else {
                //auto update
                $draft->title = $input['top_aisatsu_title'];
                $draft->memo  = $input['top_aisatsu_text'];
                $draft->save();
                return json_encode(array('status' => 'success'));
            }
        } else {
            return json_encode(array('status' => 'error'));
        }
    }
}
