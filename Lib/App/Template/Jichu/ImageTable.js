  //自定义的tip效果组件
  Vue.component('tip-preview-image',{
    props:['row'],
    data : function() {
      return {
        dialogFormVisible:true,
        form:{},
      };
    },
    template: `
      <el-popover v-if="row.imageSrc" trigger="hover" placement="top">
        <p><img :src='row.imageSrc' style='max-height:480px;'></p>
        <div slot="reference" class="name-wrapper">
          <el-tag size="medium"><i class='el-icon-picture-outline'></i></el-tag>
        </div>
      </el-popover>
    `,
  });