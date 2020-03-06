
  //以下代码在子模版中实现
  //高级功能中的按钮触发
  callbacks['btnExport:click'] = function() {
    // console.log(this);
    this.$message("导出本页按钮点击事件111",'success');
    // console.log("this,arguments",this,arguments);
  }
  //高级功能中的按钮触发
  callbacks['btnExportAll:click'] = function() {
    this.$message("导出全部按钮点击事件",'success');
    // console.log("this,arguments",this,arguments);
  }
  //高级功能中的按钮触发
  callbacks['btnRemoveAll:click'] = function() {
    this.$message("删除全部按钮点击事件",'info');
    // console.log("this,arguments",this,arguments);
  }
  //高级功能中的按钮触发
  callbacks['btnSetOver:click'] = function() {
    this.$message("标记完成按钮点击事件",'error');
    // console.log("this,arguments",this,arguments);
  }
  //高级功能中的按钮触发
  callbacks['btnSelection:click'] = function() {
    var rows = this.multipleSelection;
    this.$message(`共选中${rows.length}条记录`,'warning');
  }
  callbacks['userFuncRow'] = function(row,options) {
    alert('sontpl中的userFuncRow自定义功能触发');
  }
  //每行编辑按钮中的dialog插槽点击后触发
  //row为当前行记录
  callbacks['btnClientMemo:click'] = function(row) {
    //定义组件的option
    var opt = {
      type : 'user-dialog',
      //必须提供name参数,方便返回实例
      //使用this.$refs.myDialog[0]获得实例
      name : 'myDialog',
      //将当前记录作为属性传入组件
      row:row,
    };
    this.makeNewComp(opt);
    //等dom元素创建后才能得到组件实例
    this.$nextTick(()=>{
      var dialog = this.$refs.myDialog[0];
      dialog.title = `为${row.compName}设置联系人,这是动态创建的弹窗组件`;
      dialog.show();
    });
  }
  //行操作按钮点击后触发
  callbacks['btnClientMemo1:click'] = function(row) {
    this.$message('也可以另外创建组件进行');
    // this.$message(`共选中${rows.length}条记录`,'warning');
  }


  //用户点击编辑按钮组中的自定义按钮时打开自定义组件
  //注意必须在new vue之前
  Vue.component('user-dialog',{
    mounted:function() {
      // dump('row作为属性传入组件,不可修改',this.row);
    },
    props:[],
    data : function() {
      return {
        //必须参数,每次按钮点击后,这个变量都会变更为当前行
        row : {},
        //可选参数
        dialogFormVisible:false,
        form:{},
        title:'弹窗',
      };
    },
    template: `
      <el-dialog :title="title" :visible.sync="dialogFormVisible">
        {{row.compCode}}
        <el-form :model="form">
          <el-form-item label="联系人1" label-width="120px">
            <el-input v-model="form.name" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="联系人2" label-width="120px">
            <el-select v-model="form.region" placeholder="请选择活动区域">
              <el-option label="区域一" value="shanghai"></el-option>
              <el-option label="区域二" value="beijing"></el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
        </div>
      </el-dialog>
    `,
    methods:{
      show : function(row) {
        this.row = row;
        this.dialogFormVisible = true;
      },
      hide : function() {
        this.dialogFormVisible = false;
      }
    }
  });

  Vue.component('user-dialog1',{
    props:[],
    data : function() {
      return {
        dialogFormVisible:true,
        form:{},
      };
    },
    template: `<span>asdfasdf</span>`,
  });

  //自定义的tip效果组件
  Vue.component('tip-compName',{
    props:['row','index'],
    data : function() {
      return {
        dialogFormVisible:true,
        form:{},
      };
    },
    template: `
      <el-popover trigger="hover" placement="top">
        <p>编码: {{ row.compCode }}</p>
        <p>姓名: {{ row.compName }}</p>
        <p>住址: {{ row.address }}</p>
        <p>手机: {{ row.mobile }}</p>
        <div slot="reference" class="name-wrapper">
          <el-tag size="medium">{{ row.compName }}</el-tag>
        </div>
      </el-popover>
    `,
  });

  //单击checkbox改变行背景色,或者发起ajax请求
  Vue.component('checkbox-change-color',{
    props:['row','index'],
    data : function() {
      return {
        dialogFormVisible:true,
        form:{},
      };
    },
    template: `
      <el-checkbox @change="changeColor"></el-checkbox>
    `,
    methods : {
      changeColor : function(val) {
        dump(this,val);
        this.$message(`第${this.index}行的颜色将进行修改`);
        this.row.__bgColor=val?"#ccc":"";
      }
    }
  });