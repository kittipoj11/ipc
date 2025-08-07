DECLARE @_FDate DATE = '2024-04-01'
DECLARE @_TDate DATE = '2024-04-30'
DECLARE @_FLoc NVARCHAR(20) = 'IMP' --IMP

DECLARE @USER TABLE
(
       USER_ NVARCHAR(8),
       DEPTCODE NVARCHAR(8),
       DEPTNAME NVARCHAR(80),
       PARTITION BIGINT,
       UNIQUE NONCLUSTERED (USER_, DEPTCODE, PARTITION)
)
INSERT INTO @USER(USER_, DEPTCODE, DEPTNAME, PARTITION)
select distinct d1.USER_, d8.OMOPERATINGUNITNUMBER as [DeptCode], d8.NAME as [DeptName], d1.PARTITION from DIRPERSONUSER d1
inner join DIRPERSONNAME d2
on d2.PERSON = d1.PERSONPARTY and d2.PARTITION = d1.PARTITION AND d2.VALIDTO > getdate()
inner join HCMWORKER d3
on d3.PERSON = d2.PERSON and d3.PARTITION = d2.PARTITION
inner join HCMPOSITION d4
on d4.POSITIONID = d3.PERSONNELNUMBER and d4.PARTITION = d3.PARTITION
inner join HCMPOSITIONWORKERASSIGNMENT d5
on d5.POSITION = d4.RECID and d5.PARTITION = d4.PARTITION AND d5.VALIDTO > getdate()
--delete--on d5.WORKER = d3.RECID and d5.PARTITION = d3.PARTITION
inner join HCMPOSITIONDETAIL d6
on d6.POSITION = d5.POSITION and d6.PARTITION = d5.PARTITION AND d6.DEPARTMENT <> 0 AND d6.VALIDTO > getdate()
--delete--on d6.POSITION = d4.RECID and d6.PARTITION = d4.PARTITION
inner join DimAttributeOMDepartment d7
on d7.KEY_ = d6.DEPARTMENT and d7.PARTITION = d6.PARTITION
inner join DMFOMOPERATINGUNITENTITY d8
on d8.OMOPERATINGUNITNUMBER = d7.Value and d8.PARTITION = d7.PARTITION

DECLARE @TRACKING TABLE
(
       CONTEXTRECID BIGINT,
       INSTANCENUMBER NVARCHAR(20),
       PARTITION BIGINT,
       RECID BIGINT, 
       USER_ NVARCHAR(8),
       CREATEDDATETIME datetime
       --,UNIQUE NONCLUSTERED (CONTEXTRECID, PARTITION)
)
INSERT INTO @TRACKING (CONTEXTRECID,INSTANCENUMBER,PARTITION,RECID, USER_, CREATEDDATETIME) 
select w1.CONTEXTRECID, INSTANCENUMBER, w1.PARTITION, w1.RECID, USER_, wt.CREATEDDATETIME 
from WORKFLOWTRACKINGSTATUSTABLE w1
inner join (select CONTEXTRECID,max(CREATEDDATETIME) as max_CREATEDDATETIME, PARTITION 
                    from WORKFLOWTRACKINGSTATUSTABLE group by CONTEXTRECID, PARTITION) w2
       on w1.CONTEXTRECID = w2.CONTEXTRECID and w1.CREATEDDATETIME = w2.max_CREATEDDATETIME and w1.PARTITION = w2.PARTITION
inner join WORKFLOWTRACKINGTABLE wt
       on wt.WORKFLOWTRACKINGSTATUSTABLE = w1.RECID and wt.PARTITION = w1.PARTITION and wt.TRACKINGTYPE = 4
inner join WORKFLOWSTEPTABLE wst
       on wt.WORKFLOWSTEPTABLE = wst.RECID and wt.ELEMENTID = wst.ELEMENTID and wt.STEPID = wst.STEPID and wt.PARTITION = wst.PARTITION and wst.[NAME] ='Head of department purchasing'
AND (wt.CREATEDDATETIME  <> '2020-12-22 05:24:23.000' AND  wt.CREATEDDATETIME <> '2021-01-05 11:34:47.000')
;

DECLARE @POLastVersion TABLE
(
       PURCHID NVARCHAR(20),
       PURCHORDERDATE datetime,
       PURCHTABLEVERSION BIGINT,
       PARTITION BIGINT,
               createdBy NVARCHAR(8),
       UNIQUE NONCLUSTERED (PURCHID, PURCHORDERDATE, PURCHTABLEVERSION, PARTITION,createdBy)
)
INSERT INTO @POLastVersion(PURCHID, PURCHORDERDATE, PURCHTABLEVERSION, PARTITION,createdBy)
select v1.PURCHID, v1.PURCHORDERDATE, v1.PURCHTABLEVERSION, v1.PARTITION,v1.createdBy
from VENDPURCHORDERJOUR v1
inner join (select purchid, max(purchtableversion) PURCHTABLEVERSION, VENDPURCHORDERJOUR.PARTITION
                    from VENDPURCHORDERJOUR
                    inner join @USER PC0000
                    on PC0000.USER_ = VENDPURCHORDERJOUR.CREATEDBY AND  PC0000.PARTITION = VENDPURCHORDERJOUR.PARTITION
                    WHERE PC0000.DEPTCODE Like 'PC%'   
                    group by purchid, VENDPURCHORDERJOUR.PARTITION) v2
       on v2.purchid = v1.purchid and v2.PURCHTABLEVERSION = v1.PURCHTABLEVERSION and v2.PARTITION = v1.PARTITION
;
--select case poline.isdeleted 
--             when 1 then 'Deleted on PO' 
--             when 0 then 
--                    case when prline.itemid is null
--                           then 'No PR'
--                          else ''
--                    end 
--             else 'N/A'
--             end as [Flag]
select prtab.PURCHREQID
, poline.PURCHID as [PoNo]
, poline.LINENUMBER as [PoDt]
, convert(nvarchar(10),polast.PURCHORDERDATE,103) as [PoDate]
,''+rtrim(ltrim(replace(replace(replace(replace(term.REMARK,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [CreditTerm]
,''+rtrim(ltrim(replace(replace(replace(replace(prtab.PURCHREQID,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [PRNo]

,''+rtrim(ltrim(replace(replace(replace(replace(prtab.PURCHREQNAME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [PRDesc]
,''+rtrim(ltrim(replace(replace(replace(replace(PURCHSTATUS.ENUMITEMLABEL,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [ApprStatus]
,''+rtrim(ltrim(replace(replace(replace(replace(docstatus.ENUMITEMLABEL,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [DocStatus]
,''+rtrim(ltrim(replace(replace(replace(replace(prtab.IVZ_INVENTLOCATIONID_CT,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [LocationCode]
,''+rtrim(ltrim(replace(replace(replace(replace(LOC.NAME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [LocationName]

,''+rtrim(ltrim(replace(replace(replace(replace(prline.ITEMID,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [Product pr]
,''+rtrim(ltrim(replace(replace(replace(replace(poline.ITEMID,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [Product po]
,''+rtrim(ltrim(replace(replace(replace(replace(pritem.SEARCHNAME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [ProductName po]
,''+rtrim(ltrim(replace(replace(replace(replace(poline.NAME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [ProductName po]
,''+rtrim(ltrim(replace(replace(replace(replace(poline.PURCHUNIT,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [Unit]
,''+rtrim(ltrim(replace(replace(replace(replace(potab.DELIVERYNAME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [DeliveryPointNm]

,''+rtrim(ltrim(replace(replace(replace(replace(potab.DELIVERYDATE,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [DeliDate]
,''+rtrim(ltrim(replace(replace(replace(replace(poline.VENDACCOUNT,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [VendorCode]
,''+rtrim(ltrim(replace(replace(replace(replace(vend_party.NAME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [VendorName]
,''+rtrim(ltrim(replace(replace(replace(replace(vend_addr.ADDRESS,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' ADDRESS_

,''+rtrim(ltrim(replace(replace(replace(replace(poline.PURCHQTY,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [OrdQty]
,''+rtrim(ltrim(replace(replace(replace(replace(poline.PURCHRECEIVEDNOW ,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [RcvQty]

, poline.DISCPERCENT as [DiscPercent]
, CASE WHEN poline.DISCPERCENT<>0 THEN ((poline.PURCHPRICE*poline.DISCPERCENT)/100)*poline.PURCHQTY  WHEN poline.DISCAMOUNT<>0 THEN poline.DISCAMOUNT*poline.PURCHQTY  END AS "DisCountAmt"
, poline.OVERDELIVERYPCT as [QuantityDeviation]
, poline.PURCHPRICE as [Price]
, poline.LINEAMOUNT as [NetAmt]

, tax.TAXVALUE as [TaxRate]
, poline.TAXITEMGROUP as [TaxType]
, (poline.LINEAMOUNT*tax.TAXVALUE)/100 as [TaxAmt]
, (poline.LINEAMOUNT*(100+tax.TAXVALUE))/100 as [TotalAmt]
,''+rtrim(ltrim(replace(replace(replace(replace(prtab.IVZ_COMMENT_CT,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [PrComment]

,''+rtrim(ltrim(replace(replace(replace(replace(prtab.SUBMITTEDBY,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [ApprName1]
,''+rtrim(ltrim(replace(replace(replace(replace(prtab.SUBMITTEDDATETIME,'',''),char(13),''),char(10),''),char(13)+char(10),'')))+'' [ApprDate1]

, ws.USER_ as [ApprName2]
, ws.CREATEDDATETIME as [ApprDate2]

, prtab.CREATEDBY as [CreatedBy]
, prtab.CREATEDDATETIME as [CreatedDate]
, dept2.DeptName as [CreatedDepName]
, t.DESCRIPTION as [PrType]
, dept.NAME as [DepName]

from @POLastVersion polast
inner join PURCHTABLE potab
       on potab.PURCHID = polast.PURCHID and potab.PARTITION = polast.PARTITION 
inner JOIN PURCHLINEALLVERSIONS poline
       ON poline.PURCHTABLEVERSIONRECID = polast.PURCHTABLEVERSION AND poline.PARTITION = polast.PARTITION --AND PLAV.ISARCHIVED = 0

left join PURCHREQLINE prline
       on prline.LINEREFID = poline.PURCHREQLINEREFID and prline.PARTITION = poline.PARTITION
left join PURCHREQTABLE prtab
on prtab.RecId = prline.PurchReqTable and prtab.PARTITION = prline.PARTITION and prtab.REQUISITIONSTATUS <> 0

------Pom
left join ECORESPRODUCT pritem
on pritem.DISPLAYPRODUCTNUMBER = prline.ITEMID and pritem.PARTITION = prline.PARTITION

---- Tracking
left join @TRACKING ws
on prtab.RECID = ws.CONTEXTRECID and prtab.PARTITION = ws.PARTITION

--Department1
left join [dbo].[DimensionAttributeValueSetItemView] dim1
on dim1.DIMENSIONATTRIBUTEVALUESET = prline.DEFAULTDIMENSION AND dim1.PARTITION = prline.PARTITION and dim1.DIMENSIONATTRIBUTE = '5637145326'--(5637145326 is 'Department')
--delete--left join DimensionAttribute dept
--delete-- on dept.RECID = d1.DIMENSIONATTRIBUTE and dept.NAME = 'Department'
left join DimAttributeOMDepartment dept
on dept.VALUE = dim1.DISPLAYVALUE and dept.KEY_ = dim1.ENTITYINSTANCE AND dept.PARTITION = dim1.PARTITION

left join INVENTLOCATION LOC
on LOC.INVENTLOCATIONID = prtab.IVZ_INVENTLOCATIONID_CT and LOC.PARTITION = prline.PARTITION
left join VENDTABLE vend
on vend.ACCOUNTNUM = poline.VENDACCOUNT and vend.dataareaid ='vir'
LEFT join DIRPARTYTABLE vend_party
on vend_party.RECID = vend.PARTY and vend_party.PARTITION = vend.PARTITION
left join LOGISTICSPOSTALADDRESS vend_addr
on vend_addr.RECID = vend_party.PRIMARYADDRESSLOCATION and vend_addr.PARTITION = vend_party.PARTITION
left join TAXDATA tax
on tax.TAXCODE=poline.TAXITEMGROUP and tax.DATAAREAID = poline.DATAAREAID and tax.PARTITION = poline.PARTITION

---- Status
left join SRSANALYSISENUMS PurchStatus
on PurchStatus.ENUMITEMVALUE = potab.PURCHSTATUS and PurchStatus.ENUMNAME = 'PurchStatus' and PurchStatus.LANGUAGEID = potab.LANGUAGEID 
left join SRSANALYSISENUMS docstatus
on docstatus.ENUMITEMVALUE = potab.DOCUMENTSTATUS and docstatus.ENUMNAME = 'EMSFlowOriginType' and docstatus.LANGUAGEID = potab.LANGUAGEID 
 
----left join LOGISTICSPOSTALADDRESS addr
----on addr.RECID = prline.DELIVERYPOSTALADDRESS and addr.PARTITION = prline.PARTITION

left join PURCHREQBUSINESSJUSTIFICATIONCODES t
on prtab.BUSINESSJUSTIFICATION = t.RECID and t.PARTITION = prtab.PARTITION
left join IVZ_PURCHTABLEEXT term
on term.PURCHTABLE = potab.RECID and term.DATAAREAID = potab.DATAAREAID and term.PARTITION = potab.PARTITION

---- CreatedDepName
left join @USER dept2
on dept2.USER_ = prtab.CREATEDBY  and dept2.PARTITION = prtab.PARTITION
where 1=1
AND potab.DATAAREAID = @_FLoc
AND CAST(polast.PURCHORDERDATE AS DATE) BETWEEN @_FDate AND @_TDate
AND poline.PURCHID not like 'PO%'
--AND potab.purchid in ('IMPO20003191')
--AND polast.createdBy in ('Siriyako')
--AND potab.purchid in('REPO20000228')
--AND POJO.PURCHORDERDOCNUM like '%-1'
order by 2,3,4
