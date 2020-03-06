<{*  // 输出文件头，表明是要输出 excel 文件
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=test.xls"); //<----- 这里，改成 test.xls 也可以，这样就可以直接用 Excel 打开了。不过本质还是 xml，用记事本打开就知道了。所以打开后，再另存一下，存为真正的 xls 格式。

        $smarty->display('Export2Excel.tpl');*}><?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>software</Author>
  <LastAuthor>software</LastAuthor>
  <Created>2008-02-28T08:22:38Z</Created>
  <Company>zh</Company>
  <Version>11.8107</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>9000</WindowHeight>
  <WindowWidth>11700</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>15</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="12"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s23">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s24">
  <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="16" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s25">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="宋体" x:CharSet="134"/>
  </Style>
  <Style ss:ID="s27">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s28">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="宋体" x:CharSet="134"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Sheet1">
  <Table x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="62" ss:DefaultRowHeight="15">
   <Row ss:AutoFitHeight="0" ss:Height="25.5">
   <{assign var=cross value=$arr_field_info|@count}>
    <Cell <{if $cross>1}>ss:MergeAcross="<{$cross-1}>"<{/if}> ss:StyleID="s24"><Data ss:Type="String"><{$title}></Data></Cell>
   </Row>
   <Row ss:AutoFitHeight="0">
    <{foreach from=$arr_field_info item=item key=key}>
    <{if $key != '_edit'}>
      <Cell ss:StyleID="<{if $item.align=='right'}>s23<{else}>s27<{/if}>"><Data ss:Type="String"><{if $item|@is_string==1}><{$item}><{else}><{$item.text}><{/if}></Data></Cell>
    <{/if}>
	<{/foreach}>
   </Row>
   <{foreach from=$arr_field_value item=field_value}>
   <Row ss:AutoFitHeight="0">
    <{foreach from=$arr_field_info item=item key=key}>
    <{if $key != '_edit'}>
      <{assign var=foo value="."|explode:$key}>
  		    <{assign var=key1 value=$foo[0]}>
  		    <{assign var=key2 value=$foo[1]}>
  			<{assign var=key3 value=$foo[2]}>
        <{if $key2==''}><{assign var=_v value=$field_value.$key|default:''|escape:'html'}><{elseif $key3==''}><{assign var=_v value=$field_value.$key1.$key2|default:''|escape:'html'}><{else}><{assign var=_v value=$field_value.$key1.$key2.$key3|default:''|escape:'html'}><{/if}>
      <Cell ss:StyleID="<{if $item.align=='right'}>s25<{else}>s28<{/if}>"><Data ss:Type="<{if $_v|@is_numeric}>Number<{else}>String<{/if}>"><{$_v}></Data></Cell>
    <{/if}>
	<{/foreach}>
   </Row>
   <{/foreach}>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
  <PageSetup>
    <Header x:Margin="0.51181102362204722"/>
    <Footer x:Margin="0.51181102362204722"/>
    <PageMargins x:Bottom="0.39370078740157483" x:Left="0.74803149606299213"
     x:Right="0.74803149606299213" x:Top="0.39370078740157483"/>
   </PageSetup>
   <Unsynced/>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>300</HorizontalResolution>
    <VerticalResolution>300</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>4</ActiveRow>
     <ActiveCol>1</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Sheet2">
  <Table ss:ExpandedColumnCount="0" ss:ExpandedRowCount="0" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="14.25"/>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <Unsynced/>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Sheet3">
  <Table ss:ExpandedColumnCount="0" ss:ExpandedRowCount="0" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="14.25"/>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <Unsynced/>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
