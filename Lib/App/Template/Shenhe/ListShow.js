
Vue.component('dilog-shenhe-edit',{
  props : {
    row:{required:true},
    filed:{required:false},
    auth:{required:false},
    tableId:{required:true},
    model:{required:true},
  },
  data : function() {
      // var showText,textColor;
      // if(!this.filed){
      //   this.filed = 'shenhe';
      // }
      // if(this.row[this.filed] == ''){
      //   showText = '未审核';
      //   textColor = 'blue';
      // }else if(this.row[this.filed] == 'yes'){
      //   showText = '通过';
      //   textColor = 'green';
      // }else if(this.row[this.filed] == 'no'){
      //   showText = '不通过';
      //   textColor = 'red';
      // }else if(this.row[this.filed] == 'ing'){
      //   showText = '审核中';
      //   textColor = 'orange';
      // }
    return {
      dialogShenheVisibleIframe:false,
      dialogShenheLogVisibleIframe:false,
      titleText:'',
      shenheTitle:(this.row.shenheDisabled ? this.row.shenheDisabled : '审核操作'),
      disabled:(this.row.shenheDisabled ? 'disabled' : false),
      actionUrl:'<{url controller=Shenhe_Shenhe action=Edit}>',
      actionUrlLog:'<{url controller=Shenhe_Shenhe action=Log}>',
      showCount:0,
      // paramsData:{'showText':showText,'textColor':textColor}
    };
  },
  computed:{
    showText: function(){
      var _showText,textColor;
      if(!this.filed){
        this.filed = 'shenhe';
      }
      if(this.row[this.filed] == ''){
        _showText = '未审核';
        textColor = 'blue';
      }else if(this.row[this.filed] == 'yes'){
        _showText = '通过';
        textColor = 'green';
      }else if(this.row[this.filed] == 'no'){
        _showText = '不通过';
        textColor = 'red';
      }else if(this.row[this.filed] == 'ing'){
        _showText = '审核中';
        textColor = 'orange';
      }
      // console.log({text:_showText ,color:textColor});
      return {text:_showText ,color:textColor};
    }
  },
  template: `
    <div class="">
      <a href="javascript:;" title="点击查看审核详情" v-bind:style="{color:showText.color,width:'40px','display':'inline-block'}" @click="dialogShenheLogVisibleIframe = true">{{showText.text}}</a>&nbsp;
      <el-button v-if="auth" :title="shenheTitle" :disabled='disabled' size="mini" icon="el-icon-check" type="primary" circle @click="_handleShowShenhe" style="padding:3.5px;margin-left:3px;"></el-button>
      <el-dialog
        :title="titleText"
        :fullscreen="false"
        :visible.sync="dialogShenheVisibleIframe"
        width="700px"
        :before-close="handleShenheClose"
        >
        <div style="width:100%;height:450px;">
          <iframe ref='shenheIframe' width='100%' height='100%' :src="actionUrl" frameborder="0"></iframe>
        </div>
      </el-dialog>
      <el-dialog
        :title="titleText"
        :fullscreen="false"
        :visible.sync="dialogShenheLogVisibleIframe"
        width="700px"
        :before-close="handleShenheClose"
        >
        <div style="width:100%;height:450px;">
          <iframe width='100%' height='100%' :src="actionUrlLog" frameborder="0"></iframe>
        </div>
      </el-dialog>
    </div>
  `,
  methods: {
      handleShenheClose(done) {
        // console.log(this);
        done();
        this.$root._getRows();
      },
      _handleShowShenhe(){

        this.showCount += 1;
        this.dialogShenheVisibleIframe = true;
        if(this.showCount > 1){
          // console.log('第加载',this.showCount);
          this.$refs.shenheIframe.contentWindow.location.reload(true);
        }
      }
  },
  mounted : function() {
    this.actionUrl += "&model="+this.model+"&tableId="+this.row[this.tableId];
    this.actionUrlLog += "&model="+this.model+"&tableId="+this.row[this.tableId];
  }
});