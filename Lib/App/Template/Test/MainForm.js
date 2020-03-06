//以下代码在sontpl中实现
  {
    // var callbacks=[];
    callbacks['compCode:change'] = function(val) {
      this.$message("客户编码改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['compName:change'] = function(val) {
      this.$message("客户编码改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['people:select'] = function(val) {
      this.$message("联系人选中,onselect事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['createDate:change'] = function(val) {
      this.$message("创建日期改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['vDate:change'] = function(val) {
      this.$message("日期范围改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['compFrom:change'] = function(val) {
      dump(val);
      this.$message("客户来源改变触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['traderId:change'] = function(val) {
      this.$message("联系人改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['isStop:change'] = function(val) {
      this.$message("联系人改变事件触发");
      console.log("this,arguments",arguments);
    }
    callbacks['associateClientId:open'] = function(dialog) {
      this.$message("弹出选择上家客户,open事件触发,增加参数");
      //可以在 dialog.action 中加入 get参数,
      //dialog.action += '&key=aaa';
      //也可以直接在dialog.params中加入参数
      //dialog.params.key='bbb';
      console.log("this,arguments",arguments[0]);
    }
    callbacks['associateClientId:select'] = function(row,e) {
      this.$message("弹出选择上家客户,选中回调事件触发");
      console.log("this,arguments",arguments);
    }
    //弹出多选回调事件,
    //rows:所有选中的记录集
    callbacks['xiajia:select'] = function(rows) {
      this.$message(`弹出选择下家客户,选中回调事件触发,共选中${rows.length}条记录`);
      console.log("this,arguments",arguments);
    }
    callbacks['associateClientId1:open'] = function(rows) {
      this.$message(`associateClientId1:open fired`);

    }
    callbacks['associateClientId1:select'] = function(rows) {
      this.$message(`associateClientId1:select fired`);

    }
    callbacks['pic:remove'] = function(file, fileList) {
      this.$message("图片删除触发");
      // console.log("this,arguments",arguments);
    }
    callbacks['pic:success'] = function(response, file, fileList) {
      this.$message("图片上传成功触发");
      // console.log("this,arguments",arguments);
    }
    callbacks['file:remove'] = function(file, fileList) {
      this.$message("文件删除触发");
      // console.log("this,arguments",arguments);
    }
    callbacks['file:success'] = function(response, file, fileList) {
      this.$message("文件上传成功触发");
      // console.log("this,arguments",arguments);
    }
  }