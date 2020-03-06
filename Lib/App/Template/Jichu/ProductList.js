//弹框组建
Vue.component('data-update-dialog',{
    props:[],
    data : function() {
      return {
        dialogFormVisible:false,
        form:{},
        title:'弹窗',
        progress:0,
        totalCount:'连接环思服务器...',
        disableUpdateData : true,
        updateing : false,
        eventSource : false
      };
    },
    template: `
      <el-dialog :title="title" :visible.sync="dialogFormVisible" width="40%" :close-on-press-escape="false" :before-close="handleClose">
        <div style="text-align:center;">
          <el-progress type="circle" :percentage="progress"></el-progress>
        </div>
        <div slot="footer" class="dialog-footer">
          <el-button @click="stopUpdate">停止同步</el-button>
          <el-button type="primary" @click="startUpdate" :disabled="disableUpdateData">开始同步({{totalCount}})</el-button>
        </div>
      </el-dialog>
    `,
    methods:{
      show : function() {
        this.dialogFormVisible = true;
        this.beforeUpdate();
      },
      hide : function() {
        this.dialogFormVisible = false;
      },
      stopUpdate : function(){
        this.$confirm('确认停止同步数据？')
          .then(_ => {
              var url = "<{url controller=$smarty.get.controller action=StopUpdate}>";
              this.$http.post(url, {platfrom:'PC'})
                .then((response)=> {
                  this.disableUpdateData = true;
                  this.updateing = false;
                  this.$message(response.data.msg);
                })
                .catch((error)=>{
                  this.$message('停止失败');
                });

              //开始后，需要获取进度情况
              this.beforeUpdate();
          })
          .catch(_ => {});
      },
      startUpdate : function(){
        this.$confirm('同步数据需要一段时间，是否同步？')
          .then(_ => {
            this.disableUpdateData = true;
            this.updateing = true;
            // console.log('startUpdate' );
            //开始同步数据
            var url = "<{url controller=$smarty.get.controller action=Synchrodata}>";
            this.$http.post(url, {platfrom:'PC',startIndex:0})
              .then((response)=> {
                this.$message(response.data.msg);
                if(response.data.success){
                  this.getProgress();
                }
              })
              .catch((error)=>{
                this.$message('请求服务器失败');
              });


          })
          .catch(_ => {});
      },
      getProgress : function(){
        var that = this;
        var urlProgress = "<{url controller=$smarty.get.controller action=Syncprogress}>";

        // debugger;
        if(this.eventSource){
          this.eventSource.close();
        }
        this.eventSource = new EventSource(urlProgress+"&progress="+that.progress);
        this.eventSource.onmessage = function(event) {
            var data = eval('('+event.data+')');
            that.eventSource.url = urlProgress + "&progress=" + data.progress;
            if(data.success){
              that.progress = data.progress;
              if(data.progress == 100){
                that.disableUpdateData = true;
                that.updateing = false;
                that.eventSource.close();
                that.totalCount = '数据同步完成';
              }
            }else if(data.status == 'false'){
              that.eventSource.close();
            }
        };
      },
      handleClose : function(done){
        // console.log('beforeClose' ,done);
        if(this.updateing){
          this.$confirm('关闭不会暂停数据同步，确认关闭？')
            .then(_ => {
              this.eventSource.close();
              this.eventSource = false;
              done();
            })
            .catch(_ => {});
        }else{
          done();
        }
      },
      beforeUpdate : function(){
        //判断是否后台已经在同步数据中
        var url = "<{url controller=$smarty.get.controller action=Beforesynchrodata}>";
        this.$http.post(url, {platfrom:'PC'})
          .then((response)=> {
            if(response.data.success){
              this.totalCount = response.data.msg + '条';
              this.disableUpdateData = false;
            }else if(response.data.status == 'true'){
              this.totalCount = response.data.msg || '数据同步中...';
              this.disableUpdateData = true;
              this.updateing = true;
              //表示同步在进行中
              this.getProgress();
            }else{
              this.totalCount = response.data.msg || '连接环思异常';
            }
          })
          .catch((error)=>{
            this.$message('数据获取出错');
          });
        }
    },
    mounted:function(){
      this.beforeUpdate();
    }
  });


callbacks['btnUpdateProducts:click'] = function(row) {
  // console.log(row);
  //定义组件的option
  var opt = {
    type : 'data-update-dialog',
    //必须提供name参数,方便返回实例
    //使用this.$refs.myDialog[0]获得实例
    name : 'myDialog',
  };
  this.makeNewComp(opt);
  //等dom元素创建后才能得到组件实例
  this.$nextTick(()=>{
    var dialog = this.$refs.myDialog[0];
    dialog.title = `更新环思产品档案数据`;
    dialog.show();
  });
}