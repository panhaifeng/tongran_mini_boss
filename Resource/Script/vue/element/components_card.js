//文本框
Vue.component('comp-card',{
  props:['cardWidthCol','card-height','card-title'],
  data:function(){
    return {
      colmd : 'layui-col-md' + this.cardWidthCol,
      colxs : ' layui-col-xs12',
      pullCenter : 'card-pull-center'
    }
  },
  template: `
    <div v-bind:class="[colmd ,colxs ,pullCenter]">
      <div class="layui-card">
        <div class="layui-card-header" v-if="cardTitle!=''">{{cardTitle||''}}</div>
        <div class="layui-card-body layui-card-body-formEle" ref="cardBody">
          <slot name="body"></slot>
        </div>
        <div class="layui-card-footer">
          <div class="layui-input-block layui-input-block-center">
            <div class="layui-footer" style="left: 0;">
              <slot name="footer"></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  mounted:function(){
    var bodyHeight = this.cardHeight || '600';
    if(bodyHeight == '100%'){
      bodyHeight = document.documentElement.clientHeight || document.body.clientHeight;
      bodyHeight = bodyHeight - 156;
    }
    this.$refs.cardBody.style.cssText = "max-height:"+bodyHeight+'px;overflow:auto;min-height:150px;';
    console.log(this.$refs.cardBody.style.cssText);
  }
});