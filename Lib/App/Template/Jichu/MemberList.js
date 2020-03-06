callbacks['shenheYes:click'] = function() {
    var rows = this.multipleSelection;
    if(!rows.length){
      this.$message('请先选中待操作行','warning');
      return false;
    }
    console.log(rows);
    var a2mIds = [];
    rows.forEach((item,i)=>{
      a2mIds.push(item.a2mId);
    });
    console.log(a2mIds);

    var tipTitle = '确认通过审核吗';
    this.$confirm(tipTitle, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      var url = "<{url controller=$smarty.get.controller action='ShenheConfirm'}>";
      var params = {id:a2mIds,'shenhe':'yes'};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$message(res.data.msg||'操作失败','error');
          // console.log(res);
          return false;
        }
        this.$message(res.data.msg||'操作成功','success');
        //刷新grid
        this._getRows();
        return;
      }).catch((error)=>{
        //服务器端返回success=false
        console.error(error);
      });
    }).catch(() => {
      //请求失败
      return false;
    });
}

callbacks['shenheNo:click'] = function() {
    var rows = this.multipleSelection;
    if(!rows.length){
      this.$message('请先选中待操作行','warning');
      return false;
    }
    console.log(rows);
    var a2mIds = [];
    rows.forEach((item,i)=>{
      a2mIds.push(item.a2mId);
    });
    console.log(a2mIds);

    var tipTitle = '确认不通过审核吗';
    this.$confirm(tipTitle, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      var url = "<{url controller=$smarty.get.controller action='ShenheConfirm'}>";
      var params = {id:a2mIds,'shenhe':'no'};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$message(res.data.msg||'操作失败','error');
          // console.log(res);
          return false;
        }
        this.$message(res.data.msg||'操作成功','success');
        //刷新grid
        this._getRows();
        return;
      }).catch((error)=>{
        //服务器端返回success=false
        console.error(error);
      });
    }).catch(() => {
      //请求失败
      return false;
    });
}

callbacks['shenheYes'] = function(row,options) {
    var tipTitle = '确认通过审核吗';
    this.$confirm(tipTitle, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      var url = "<{url controller=$smarty.get.controller action='ShenheConfirm'}>";
      var params = {id:row.a2mId,'shenhe':'yes'};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$message(res.data.msg||'操作失败','error');
          // console.log(res);
          return false;
        }
        this.$message(res.data.msg||'操作成功','success');
        //刷新grid
        this._getRows();
        return;
      }).catch((error)=>{
        //服务器端返回success=false
        console.error(error);
      });
    }).catch(() => {
      //请求失败
      return false;
    });
}

callbacks['shenheNo'] = function(row,options) {
    var tipTitle = '确认不通过审核吗';
    this.$confirm(tipTitle, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      var url = "<{url controller=$smarty.get.controller action='ShenheConfirm'}>";
      var params = {id:row.a2mId,'shenhe':'no'};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$message(res.data.msg||'操作失败','error');
          // console.log(res);
          return false;
        }
        this.$message(res.data.msg||'操作成功','success');
        //刷新grid
        this._getRows();
        return;
      }).catch((error)=>{
        //服务器端返回success=false
        console.error(error);
      });
    }).catch(() => {
      //请求失败
      return false;
    });
}