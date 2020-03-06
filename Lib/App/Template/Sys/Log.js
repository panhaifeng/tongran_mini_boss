  //高级功能中的按钮触发
  callbacks['btnClearLog:click'] = function() {
    var url = "<{url controller=$smarty.get.controller action=ClearLog auto=1}>";
    this.$http.post(url,{}).then((res)=>{
        if(!res.data.status) {
          this.$notify.error({
            'title':'删除失败',
            'message':'没有成功请求'
          });

          return false;
        }
        this.$notify.success({
          title:'成功',
          message:'成功清除指定日志',
        });

      }).catch(function (error) {
        this.$notify.error({
          'title':'请求失败',
          'message':'请求失败,检查配置或网络'
        });
    });
  }