Vue.prototype.$http = axios;
//总线
Vue.prototype.$bus = new Vue();

//将this.$message改造成parent.showMsg,使得调用方式和this.$message一致
if(window.parent != window) {
  Vue.prototype.$message = (text,status ,time)=>{
    var p = window.parent;
    if(!time || time == undefined){
      time = 5000;
    }
    window.top.showMsg(text,status,time);
  }

  //消息
  Vue.prototype.$notify = {
    success: function(param){
      window.top.showMsg(param.message,'success',param.time);
    },
    error:function(param){
      window.top.showMsg(param.message,'error',param.time);
    },
    warning:function(param){
      window.top.showMsg(param.message,'warning',param.time);
    }
  }
}

var dump = console.log;
//todo:woff,tiff文件的封装,layer的封装

//文本框
Vue.component('comp-text',{
  props:['fld','value','readonly','disabled'],
  template: `
    <el-input
      v-bind="fld"
      :value='value'
      @input="handleModelInput"
      @change="handleChange"
      :ref="fld.name"
      >
      <template slot="prepend" v-if="fld.prepend||fld.addonPre">
        <span v-html="fld.prepend||fld.addonPre"></span>
      </template>
      <template slot="append" v-if="fld.append||fld.addonEnd">
        <span v-html="fld.append||fld.addonEnd"></span>
      </template>
    </el-input>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    handleChange :function(val){
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    },
  }
});


//通用文本框:支持制定类型
Vue.component('comp-input',{
  props:['fld','value','readonly','disabled'],
  template: `
      <el-input
      :type="fld.inputType"
      v-bind="fld"
      :value='value'
      @input="handleModelInput"
      @change="handleChange"
      :ref="fld.name"
      ></el-input>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    handleChange :function(val){
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    },
  }
});

//文本框
Vue.component('comp-textarea',{
  props:['fld','value'],
  template: `
      <el-input
      type="textarea"
      autosize
      v-bind="fld"
      :value='value'
      @input="handleModelInput"
      @change="handleChange"
      :ref="fld.name"
      ></el-input>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    handleChange :function(val){
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    },
  }
});


//autocomplete
Vue.component('comp-autocomplete',{
  props:['fld','value'],
  /*源码中,suggestion点击后触发的动作如下:
  select(item) {
        this.$emit('input', item[this.valueKey]);
        this.$emit('select', item);
        this.$nextTick(_ => {
          this.suggestions = [];
          this.highlightedIndex = -1;
        });
      },
  */
  template: `
      <el-autocomplete
      v-bind="fld"
      :fetch-suggestions="querySearch"
      :value="value"
      @input="handleModelInput"
      @select="handleSelect"
      ></el-autocomplete>
  `,
  methods:{
    querySearch : function(queryString, cb) {
      var options  = this.fld.options || [];
      var results = queryString ? options.filter(this.createFilter(queryString)) : options;
      cb(results);
    },
    createFilter(queryString) {
      return (restaurant) => {
        return (restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    handleSelect : function(item) {
      var key = `${this.fld.name}:select`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
      // this.$emit("select", `${this.fld.name}:select`, item);
    }
  },
  mounted:function(){
    if(this.value==undefined) {
      console.warn(`数据集中的[${this.fld.name}]字段没有默认值`);
    }
  }
});

//组合输入框,通用表单中用得不多,不考虑封装,类似日期选择,客户弹出选择的控件,另外写组件,
//另外因为前后置组件变化太多,不太方便封装,可能需要高度定制组件,或者直接写静态页面.
Vue.component('comp-group-input',{
  props:['fld','value'],
  //模板写法一:直接绑定属性,
  //如果定义了v-model,那么fld.value属性失效
  template: `
      <el-input v-bind="fld" v-if="fld['bindfield']==null"></el-input>
      <el-input v-bind="fld" v-else :value='value' @input="handleModelInput"></el-input>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
  }
});

//日历
Vue.component('comp-calendar',{
  data : function(){
    return {
      'type':'date',
      'format':'yyyy-MM-dd',
      'valueFormat':'yyyy-MM-dd',
    }
  },
  props:['fld','value'],
  template: `
      <el-date-picker
        align="right"
        v-bind="fld"
        :type="type"
        :format="format"
        :value-format="valueFormat"
        :value="value"
        @input="handleModelInput"
        @change="handleChange"
      ></el-date-picker>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    handleChange : function(val) {
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    }
  },
  created : function() {
    //year/month/date/dates/ week/datetime/datetimerange/daterange
    var mapType = {
      'year':{'format':'yyyy','value-format':'yyyy'},
      'month':{'format':'yyyy-MM','value-format':'yyyy-MM'},
      'date':{'format':'yyyy-MM-dd','value-format':'yyyy-MM-dd'},
      'dates':{'format':'yyyy-MM-dd','value-format':'yyyy-MM-dd'},
      // 'week':{'format':'yyyy-MM-dd','value-format':'yyyy-MM-dd'},
      // 'datetime':{'format':'yyyy-MM-dd','value-format':'yyyy-MM-dd'},
      // 'datetimerange':{'format':'yyyy-MM-dd','value-format':'yyyy-MM-dd'},
      'daterange':{'format':'yyyy-MM-dd','value-format':'yyyy-MM-dd'},
    };
    //如果配置参数中定义了ctype(可能是日期范围),这里需要动态改变
    if(this.fld['ctype']) this.type=this.fld['ctype'];
    this.format = mapType[this.type]['format'];
    this.valueFormat = mapType[this.type]['value-format'];
  }
});

//file文件上传
//注意这个组件不好使用v-model进行简单的双向绑定,考虑回调事件中进行编写
//elment已经提供了很合理的ui,所以暂不考虑改写list-style,
//但是在form中会和其他form-item的显示有很大区别,所以一般需要另起一行显示
Vue.component('comp-image',{
  props:['fld','value'],
  data:function(){
    return {
      fileList: this.value==''?[]:this.value,
      // fileList: [
      //   {name: 'food.jpeg', url: 'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'},
      //   {name: 'food2.jpeg', url: 'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'}
      // ],
      dialogImageUrl: '',
      dialogVisible: false,
      action:this.fld.action,
      actionRemove : this.fld.actionRemove
    };
  },
  template: `
    <span>
    <el-upload
      :before-remove="beforeRemove"
      :on-exceed="handleExceed"
      :on-preview="handlePreview"
      :on-remove="handleRemove"
      :on-success="handleSuccess"
      :on-error="handelError"
      :file-list="fileList"
      list-type="picture-card"
      v-bind="fld"
      :action="fld.action"
      >
      <el-button type="primary">{{fld.title}}</el-button>

    </el-upload>
    <el-dialog :visible.sync="dialogVisible">
      <img width="100%" :src="dialogImageUrl" alt="" />
    </el-dialog>
    </span>
  `,
  methods:{

    // handleModelInput :function(val){
    //   this.$emit("input", val);
    // },
    setFileList : function(fl) {
      this.fileList = fl==''?[]:fl;
    },
    'beforeRemove' : function(file, fileList){
      return this.$confirm(`确定移除 ${ file.name }？`);
    },
    //点击文件列表项时触发
    'handlePreview' : function(file){
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
    'handelError': function(err, file, fileList) {
      this.$notify.warning('图片保存失败');
      console.error(err);
      return false;
    },
    'handleRemove' : function(file, fileList){
      //从服务器删除图片
      if(this.actionRemove) {
        var param = {url:file.url};
        this.$http.post(this.actionRemove,param).then((res)=>{
          if(!res.data.success) {
            this.$notify.error({
              'title':'服务器删除图片失败',
              'message':'response.data.success not found'
            });
            dump(res);
            return false;
          }
          this.$emit('input',fileList);
          this.$emit('changeimg',fileList,this);
          this.$notify.success({
            title:'图片删除成功',
            message:res.data.msg,
          });
          //删除回调
          var key = `${this.fld.name}:remove`;
          if(!this.$root.callbacks[key]) return;
          this.$root.callbacks[key].apply(this,arguments);
        }).catch(function (error) {
          dump(error);
          this.$notify.error({
            'title':'请求失败',
            'message':'请求失败,检查url或者其他设置'
          });
        });
      } else {
        //如果没有设置服务器删除代码,直接删除回调
        var key = `${this.fld.name}:remove`;
        if(!this.$root.callbacks[key]) return;
        this.$root.callbacks[key].apply(this,arguments);
      }

    },
    'handleExceed' : function(files, fileList){
      this.$message(`当前限制选择${this.fld.limit}个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
    },
    // 'beforeUpload': function(file) {
    //   dump('beforeUpload fired');
    // },
    'handleSuccess':function(response, file, fileList) {
      if(!response.success) {
        this.$notify.warning('图片保存存在问题,您需要重新上传!');
        return false;
      }

      if(!response.imgPath) {
        this.$notify.warning('图片保存后未返回imgPath!');
        return false;
      }
      file.url=response.imgPath;
      //格式化fileList,去掉不需要的字段信息
      var fl = [];
      fileList.forEach((item,i)=>{
        fl.push({name:item.name,url:item.url,imageId:item.imageId||item.response.imageId});
      });
      this.$emit('input',fl);
      this.$emit('changeimg',fileList,this);

      //文件上传成功后回调
      var key = `${this.fld.name}:success`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    }
  },
  mounted: function(){
    if(this.fld.action=='') {
      console.warn(`文件上传控件${this.fld.name}未定义action`);
      return ;
    }
    // dump('file mounted:this:',this);
  }
});

Vue.component('comp-file',{
  extends:Vue.component('comp-image'),//从comp-image继承
  template: `
    <el-upload
    :on-exceed="handleExceed"
    :on-remove="handleRemove"
    :before-remove="beforeRemove"
    :on-success="handleSuccess"
    :on-error="handelError"
    :file-list="fileList"
    list-type="text"
    v-bind="fld"
    >
      <el-button type="primary">{{fld.title}}</el-button>
    </el-upload>
  `,
});

//select
Vue.component('comp-select',{
  props:['fld','value'],
  // props:['fld'],
  // props:{
  //   fld : {type:Object},
  //   value : {
  //     // default:function(){
  //     //   return this.multiple ? [] : '';
  //     // },
  //     validator : function(value) {
  //       // return false;
  //       // dump(this.fld,value);
  //       // if(!this.fld) return true;
  //       // if(this.fld.multiple) dump(value);
  //       debugger;
  //       dump(this.fld,value,this);
  //       // if(this.fld.multiple && !Array.isArray(value)) return false;
  //       return true;
  //     }
  //   },
  // },
  data : function(){
    if(this.fld.multiple && !Array.isArray(this.value)) {
      console.error('comp-select组件为多选属性，props中的value属性必须提供，且必须为数组，当前值为'+this.value,this.fld);
      // return false;
    }
    return {
      displayText:''
    };
    // var v = this.value;
    // return {
    //   myValue : v,
    // };
  },
  template: `
    <el-select
      :placeholder="fld.placeholder"
      :value='value'
      @input="handleModelInput"
      @change="handleChange"
      :clearable='fld.clearable'
      :filterable='fld.filterable'
      :multiple='fld.multiple'
      :disabled='fld.disabled'
      :collapse-tags='fld.multiple'
      >
      <el-option
        v-for="item in fld.options"
        :key="item.value"
        :label="item.text"
        :value="item.value">
      </el-option>
    </el-select>
  `,
  mounted : function () {
    this.displayText = this.getDisplayText();
  },
  methods:{
    //得到描述
    getDisplayText : function(val) {
      if(!val) val = this.value;
      //根据option和value设置displayText,回显时需要用到
      var displayText='';
      if(!this.fld.multiple) {
        for(var i=0;this.fld.options[i];i++) {
          if(this.fld.options[i].value==val) {
            displayText = this.fld.options[i].text;
            break;
          }
        }
      } else {
        var temp = [];
        for(var j=0;val[j];j++) {
          for(var i=0;this.fld.options[i];i++) {
            if(this.fld.options[i].value==val[j]) {
              temp.push(this.fld.options[i].text);
            }
          }
        }
        displayText = temp.join(',');
      }
      return displayText;
    },
    handleModelInput :function(val){
      this.$emit("input", val);
      this.displayText = this.getDisplayText(val);
      //向总线提交事件，在mainson弹窗中，如果存在select控件，需要通过总线，改变cureentRow中的某个字段的值，用来显示在字表中
      //比如在弹框中选择了客户后，clientId改变的同时，必须在字表中显示对应客户的compName字段
      //构造需要传递的option
      var newVal = [];
      if(typeof(val)=='object') newVal = val;
      else newVal.push(val);
      var opt = [];
      for (var i=0;this.fld.options[i];i++) {
        var v = this.fld.options[i].value;
        if(newVal.indexOf(v)==-1) continue;
        opt.push(this.fld.options[i]);
      }
      this.$bus.$emit('selectChanged',this.fld.name,opt);
    },
    handleChange : function(item) {
      this.$emit("change", item,this);
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    }
  }
});

//checkbox
//注意下面不能使用label
Vue.component('comp-checkbox',{
  props:['fld','value'],
  template: `
      <el-checkbox
      :value="value"
      @input="handleModelInput"
      @change="handleChange"
      :true-label="fld['true-label']"
      :false-label="fld['false-label']"
      >{{fld.text}}</el-checkbox>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    //val为更新后的值
    handleChange :function(val){
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    },
  }
});

//checkbox-group
Vue.component('comp-checkbox-group',{
  props:['fld','value'],
  template: `
    <el-checkbox-group
      :value="value"
      @input="handleModelInput"
      @change="handleChange"
      >
      <el-checkbox
        v-for="(opt,i) in fld.options"
        :key="i"
        :label="opt.value"
        v-bind="opt"
        >{{opt.text}}</el-checkbox>
    </el-checkbox-group>
  `,
  methods:{
    handleModelInput :function(val){
      // this.myValue = val;
      this.$emit("input", val);

      //向总线提交事件，在mainson弹窗中，需要通过总线，改变cureentRow中的某个字段的值，用来显示在字表中
      //比如在弹框中选择了多个爱好后，爱好Id改变的同时，必须在子表中显示对应爱好的中文字段
      //构造需要传递的option
      var opt = [];
      for (var i=0;this.fld.options[i];i++) {
        var v = this.fld.options[i].value;
        if(val.indexOf(v)==-1) continue;
        opt.push(this.fld.options[i]);
      }
      this.$bus.$emit('selectChanged',this.fld.name,opt);
    },
    //val为更新后的值
    handleChange :function(val){
      // var key = `${this.fld.name}:change`;
      // if(!this.$root.callbacks[key]) return;
      // this.$root.callbacks[key].apply(this,arguments);
    },
  },
  mounted : function() {
    // if(this.value='') this.value=[];
    if(!Array.isArray(this.value)) {
      console.warn(`comp-checkbox-group组件(${this.fld.name})的默认值必须是数组,当前值为`,this.value);
    }
  }
});



//动态form,基础档案类型布局,
//不建议使用,过渡封装
Vue.component('comp-form',{
  props:['fields','row','rules','action','formWidthCol','formHeight','formTitle'],
  template: `
    <el-form
      :model="row"
      ref='ruleForm'
      :rules="rules"
      @submit.native.prevent
      >
      <comp-card
        :card-width-col="formWidthCol"
        :card-height="formHeight"
        :card-title="formTitle"
        >
        <template slot="body">
          <el-form-item
            v-for="fld,i in fields"
            :key="fld.name"
            :label="fld.title"
            :prop="fld.name"
            label-width="100px"
            >
            <component
              :is="fld.type"
              :fld="fld"
              :value="row[fld.name]"
              :displaytext="row[fld['displayKey']]"
              @input="row[fld.name]=arguments[0]"
              :ref="fld.name"
            ></component>
          </el-form-item>
        </template>
        <template slot="footer">
          <el-button type="primary" @click="submitForm('ruleForm')" :disabled="disableSubmit">立即创建</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </template>
      </comp-card>
    </el-form>
  `,
  data : function() {
    return {
      'disableSubmit':false,
    }
  },
  methods : {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (!valid) {
          dump('error submit!!');
          return false;
        }
        this.disableSubmit = true;
        dump('form submit fired,params',this.row);
        this.$http.post(this.action,this.row).then((res)=>{
          if(!res.data.success) {
            this.$notify.error({
              'title':'保存失败',
              'message':'response.data.success not found'
            });
            dump(res);
            return false;
          }
          this.$notify.success({
            title:'成功',
            message:res.data.msg,
          });
        }).catch(function (error) {
          dump(error);
          this.$notify.error({
            'title':'请求失败',
            'message':'请求失败,检查url或者其他设置'
          });
        });
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    }
  },
  mounted:function(){
    dump('form.fields',this.fields);
    dump('from.rules',this.rules);
    dump('form.action',this.action);

  }
});
//主从表单.
//不建议使用,过渡封装
Vue.component('comp-form-mainson',{

});

//高级搜索弹框
//文本框
Vue.component('comp-advsearch-dialog',{
  //根据searchItems形成相关控件
  props:[],
  data: function() {
    return {
      dialogFormVisible : false,
      params: {},
      formLabelWidth: '120px',
      //需要构造的搜索控件
      searchConfig:[],
      //配置文件中定义了所有的searchItem,
      searchItems:[],
      // items:[],
      cardBodyHeight: {'max-height':'420px','overflow':'auto','padding':'0px 15px'},
      //高级搜索条件的描述
      descAdvParams:'',
    };
  },
  template: `
    <el-dialog
      title="高级搜索"
      :visible.sync="dialogFormVisible"
      :modal="false"
      :fullscreen="false"
      custom-class="dialog-advsearch"
      top="35px"
      @open="$emit('open')"
      >
      <el-card
        class="dialog-search-box-card"
        shadow="never"
        :body-style="cardBodyHeight"
        >
        <el-form :model="params">
          <el-form-item
            v-for="(item, index) in searchItems"
            :key="index"
            :label="item.title"
            :prop="item.name"
            label-width="120px"
            >
            <component
              :is="item.type"
              :fld="item"
              :ref="item.name"
              :value="params[item.name]"
              @input="params[item.name]=arguments[0]"
            ></component>
          </el-form-item>
        </el-form>
      </el-card>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleOk">确 定</el-button>
        <el-button @click="dialogFormVisible=false">取 消</el-button>
      </div>
    </el-dialog>
  `,
  methods:{
    show : function() {
      this.dialogFormVisible = true;
    },
    hide : function() {
      this.dialogFormVisible = false;
    },
    getDescAdvParams : function() {
      var text = [];
      //dump(this.searchItems,text);
      this.searchItems.forEach((item,index)=>{
        var textJoin = item.type=='comp-text' ? "包含" : "等于";
        if(this.params[item.name]===undefined) return;
        if(this.params[item.name]=='') return;
        // dump(item.name);
        //取组件的显示文本，可能是displayText属性
        var temp = this.$refs[item.name][0].displayText || this.$refs[item.name][0].value;
        text.push(`${item.title} ${textJoin} ${temp}`);
      });
      return text.join("<br/>");
    },
    handleOk:function() {
      this.dialogFormVisible=false;
      //将已确认的搜索条件形成文字描述

      // return text.join("<br/>");
      this.descAdvParams = this.getDescAdvParams();
      //抛出
      this.$emit('select',this.params);
    },
    //根据配置信息,返回所有需要构造的搜索控件
    //所有的搜索控件在Config/Searchitems_config.php中定义好了.
    setSearchItems : function(opt) {
      this.searchConfig = opt;
      // console.log(opt);
      //从服务器获得高级搜索条目
      var url='?controller=searchitems&action=getcomps';
      //将this.searchitems转变成数组
      var temp=[]
      for(var key in opt) {
        temp.push(key);
      }
      var params={items:temp};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$notify.error({
            'title':'保存失败',
            'message':'response.data.success not found'
          });
          // dump(res);
          return false;
        }
        //开始处理返回的items
        var items = res.data.items;
        this.searchItems = items;

        //根据服务器返回的字段定义,为this.params进行初始化
        items.forEach((item,i)=>{
          //不能直接进行设置,因为params中的变量和控件存在绑定关系,简单创建变量不能生成绑定关系,必须使用Vue.set来进行设置
          // this.params[item.name]='';
          Vue.set(this.params,item.name,opt[item.name]||'');
        });
      }).catch(function (error) {
        console.error(error);
        this.$notify.error({
          'title':'请求失败',
          'message':'请求失败,检查url或者其他设置'
        });
      });
    }
  },
  mounted: function() {
    var bodyHeight = document.documentElement.clientHeight || document.body.clientHeight;
    bodyHeight = bodyHeight - 222;
    this.cardBodyHeight['max-height'] = bodyHeight+'px';

  }
});



//tablelist中详细信息展开后的样式2-同时显示订单详细和订单明细
Vue.component('comp-expand-tabs',{
  props:['row','options'],
  template: `
    <el-tabs type='border-card' id='__div_tab'>
      <template v-for="(opt,index) in options">
        <el-tab-pane :label="opt.title">
          <comp
            :is="'comp-expand-tabs-'+opt.type"
            :row="row"
            :options="opt.options"
            ></comp>
        </el-tab-pane>
      </template>
    </el-tabs>
  `,
  mounted: function() {
    // dump('comp-expand-tabs mounted',document.body.clientWidth);
    //不进行设置的话,在列多横向滚动的时候,详细区域会超出屏幕宽度,看不完整
    document.getElementById('__div_tab').style.width=(document.body.clientWidth-120) + 'px';
  }
});
//展开的面板中type=form
Vue.component('comp-expand-tabs-form',{
  props:['row','options'],
  template: `
    <el-form
      label-position="left"
      inline
      style="height:100%;overflow:auto;"
      class="demo-table-expand">
      <el-form-item
        v-for="(f,index) in options.formItems"
        :key="index"
        :label="f.text"
        >
        <span v-html="row[index]"></span>
      </el-form-item>
    </el-form>
  `,
  mounted: function() {
    // dump('comp-expand-tabs-form.mounted',this);
  }
});
//展开的面板中type=table
Vue.component('comp-expand-tabs-table',{
  props:['row','options'],
  data: function(){
    return {
      'sonKey' : this.options.sonKey,
      'data':this.row[this.options.sonKey],
      'cols':this.options.columns,
    }
  },
  template: `
    <comp-table
      :data="data"
      :cols="cols"
      height="200"
      ></comp-table>
  `,
  mounted: function() {
    // dump('comp-expand-tabs-table.mounted',this);
  }
});

//switch
Vue.component('comp-switch',{
  props:['fld','value'],
  data:function (){
    return {
      'activeText'      :this.fld.activeText ? this.fld.activeText : '是',
      'inactiveText'    :this.fld.inactiveText ? this.fld.inactiveText : '否',
      'activeValue'     :this.fld.activeValue ? this.fld.activeValue : true,
      'inactiveValue'   :this.fld.inactiveValue ? this.fld.inactiveValue : false,
      'defaultVal'      :this.value ? this.value : this.activeValue,
    }
  },
  template: `
      <el-switch
          v-bind="fld"
          @input="handleModelInput"
          @change="handleChange"
          :ref="fld.name"
          v-bind:value="defaultVal"
          v-on:input="defaultVal = $event"
          >
        </el-switch>
  `,
  methods:{
    handleModelInput :function(val){
      this.$emit("input", val);
    },
    handleChange : function(item) {
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    }
  }
});

//switch
Vue.component('comp-message-alert',{
  props:['fld'],
  data:function (){
    return {
      'type'      :this.fld.alertType ? this.fld.alertType : 'info',
    }
  },
  template: `
      <el-alert
        v-bind="fld"
        :type="type"
        :title="fld.alertTitle"
        @close="handleClose"
        >
      </el-alert>
  `,
  methods:{
    handleClose : function(item) {
      var key = `${this.fld.name}:close`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    }
  }
});

//下拉checkbox,在自定义表格可显示列时用到
Vue.component('comp-dropdown-checkbox',{
  props:['width'],
  data:function (){
    return {
    }
  },
  template: `
    <div  class="el-table-filter" style="width:200px;">
      <div class="el-table-filter__content">
        <el-scrollbar wrap-class="el-table-filter__wrap">
          <el-checkbox-group class="el-table-filter__checkbox-group" >
            <el-checkbox>id</el-checkbox>
            <el-checkbox>id1</el-checkbox>
            <el-checkbox>id2</el-checkbox>
            <el-checkbox>id3</el-checkbox>
            <el-checkbox>id</el-checkbox>
            <el-checkbox>id</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
            <el-checkbox>啊沙发上地方</el-checkbox>
          </el-checkbox-group>
        </el-scrollbar>
      </div>
    </div>
  `,
  methods:{

  },
  mounted: function() {
    // import("./test.js");
  }
});

//cascader
//ajax从后台获得tree数据,fld中只需要传入一个parentId即可
//ajax从后台获得默认值对应的路径,并进行赋值,value可以是一个子节点
Vue.component('comp-cascader',{
  props:['fld','value'],
  data : function() {
    // dump('this.value',this.value);
    return {
      urlTree:this.fld.urlTree,
      urlPath:this.fld.urlPath,
      //默认值,数组形式的value,代表节点路径
      valueArray:[],
      //树的json描述
      treeJson:[],
      //value改变时,是否需要ajax从后台获取路径,
      //在点击确认之后,value会改变,这时应该避免ajax提交
      needAjax:true,
    };
  },
  template: `
    <el-cascader
      expand-trigger="hover"
      @change="handleChange"
      :options="treeJson"
      :show-all-levels="false"
      :value="valueArray"
      @input="handleModelInput"
      @active-item-change="handleItemChange"
      filterable
      clearable
      separator
      >
    </el-cascader>
  `,
  methods:{
    handleModelInput :function(val){
      this.needAjax = false;
      var len = val.length;
      // dump('handleModelInput fired',val[len-1]);
      this.$emit("input", val[len-1]);
      this.$nextTick(function(){
        this.needAjax = true;
        // dump('after $emit fired,needAjax set to true');
      });

    },
    handleChange : function(item) {
      var key = `${this.fld.name}:change`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    },

    handleItemChange:function(val){

    }
  },
  watch : {
    'value':{
      handler:function(val,oldVal) {
        if(!this.needAjax) {
          return true;
        }
        this.$http.post(this.urlPath,{id:val}).then((res)=>{
          if(!res.data.success) {
            this.$notify.error({
              'title':'从后台获取路径失败',
              'message':'response.data.success not found'
            });
            dump(res);
            return false;
          }
          this.valueArray = res.data.path || [];
        });
      },
      immediate: true
    }
  },
  mounted: function() {
    // this.treeJson = [
    //   {value: 'zhinan',label: '指南',},
    //   {value: 'zhinan1',label: '指南1',},
    // ];
    //检查url中是否参数完整
    if(this.fld.parentId==undefined) {
      console.error('组件comp-cascader-ajax中未发现 parentId 参数');
      return;
    }
    //ajax获得树,
    this.$http.post(this.urlTree,{parentId:this.fld.parentId}).then((res)=>{
      if(!res.data.success) {
        this.$notify.error({
          'title':'从后台获取tree失败',
          'message':'response.data.success not found'
        });
        dump(res);
        return false;
      }
      this.treeJson = res.data.tree;
    });
  }
});