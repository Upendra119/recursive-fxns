   public function getNavBarRoleWise(Request $request){
        $parent_id=0;
        if($request->has('parent_id')){
          $parent_id =$request->parent_id;
        }
        if($request->role_id){
          $array=$this->buildTree($parent_id,$request->role_id);
          return response()->json([
              'code'         =>200,
              'status'       =>'success',
              'message'      =>'',
              'items'        =>$array
            ]);
        }else{  
          return response()->json([
              'code'         =>200,
              'status'       =>'fail',
              'message'      =>'Soory! Access Not Allowed'
            ]);
        }
       
      }    
    
       function buildTree($parent_id = 0,$role_id=4) {
            $branch = array();
            
            $data = DB::table('user_permissions as up')
                    ->select('ni.id','ni.name','ni.url','ni.icon','ni.badge','ni.title','ni.parent_id','roles.name as role_name')
                    ->join('nav_items as ni','ni.id','=','up.nav_item_id')
                    ->join('roles','roles.id','=','up.role_id')
                    ->where(['ni.parent_id'=>$parent_id,'up.is_active'=>1,'up.view'=>1,'up.is_deleted'=>0])
                    ->where('up.role_id',$role_id)
                    ->get()->toArray();    

            $data = array_map(function ($value) {
              if(!$value->badge){
                unset($value->badge);
              }
                      return (array)$value;
                    }, $data);
          if(count($data)){
              foreach ($data as $element) {
                  
                      $children = $this->buildTree($element['id'],$role_id);
                      if ($children) {
                          $element['children'] = $children;
                      }
                      $branch[] = $element;
              }
          }
        
            return $branch;
      }
  
