/*用户表 建表sql*/
 CREATE TABLE atccsuser(
     "id" SERIAL PRIMARY KEY,
     "username" character varying(100) NOT NULL,
     "password" character varying(200) NOT NULL,
     "status" integer NOT NULL,
     "role" integer NOT NULL,
     "description" character varying(200) NOT NULL
 );
/*用户表 建表sql*/

/*望远镜表 建表sql*/
 CREATE TABLE atlist(
     "id" serial PRIMARY KEY,
     "atid" character varying(200),
     "atname" character varying(200) NOT NULL,
     "address" character varying(200) NOT NULL,
     "longitude" character varying(100) NOT NULL,
     "latitude" character varying(100) NOT NULL,
     "altitude" character varying(100) NOT NULL,
     "aperture" character varying(100) NOT NULL
 );
/*望远镜表 建表sql*/

/*望远镜列表 即转台的固定属性表 建表sql*/ 
CREATE TABLE gimbalconf(
     "id" serial PRIMARY KEY,
	 "teleid" character varying(10), /*对应atlist表中的id字段*/
	 "ip" character varying(200),	 
     "type" character varying(200),
     "focustype" character varying,
     "maxaxis1speed" character varying(100),
     "maxaxis2speed" character varying(100),
     "maxaxis3speed" character varying(100),
     "maxaxis1acceleration" character varying(100),
     "maxaxis2acceleration" character varying(100),
     "maxaxis3acceleration" character varying(100),
     "axis1parkposition" character varying(100),
     "axis2parkposition" character varying(100),
     "axis3parkposition" character varying(100),
     "haveaxis3" character varying(100),
     "haveaxis5" character varying(100),
     "minelevation" character varying(100),
     "numtemperaturesensor" character varying(100),
     "numhumiditysensor" character varying(100),
     "canconnect" character varying(10),
     "canfindhome" character varying(10),
     "cantrackstar" character varying(10),
     "cansetobjectname" character varying(10),
     "canslewazel" character varying(10),
     "canslewderotator" character varying(10),
     "canconfigderotator" character varying(10),
     "canstop" character varying(10),
     "cansettrackspeed" character varying(10),
     "canpark" character varying(10),
     "canfixedmove" character varying(10),
     "canpositioncorrect" character varying(10),
     "cancoveroperation" character varying(10),
     "canfocusoperation" character varying(10),
     "canemergencystop" character varying(10),
     "cansavesyncdata" character varying(10),
     "cantracksatellite" character varying(10),
     "canconfigproperty" character varying(10),
     "attrversion" character varying(10),
     "attrmodifytime" character varying(100)
);/*望远镜列表 即转台的固定属性表 建表sql 结束*/


/*ccd的固定属性表 建表sql*/ 
CREATE TABLE ccdconf(
     "id" serial PRIMARY KEY,
     "ccdno" character varying(10)NOT NULL, /*ccd 序号 标识第几个ccd*/
	 "ccdid" character varying, /*ccd id*/
	 "ip" character varying(200),
	 "name" character varying,
     "teleid" character varying(10) NOT NULL, /*对应atlist表中的id字段*/ 
     "type" character varying(200),
     "xpixel" character varying(50),
     "ypixel" character varying(50),
     "xpixelsize" character varying(50),
     "ypixelsize" character varying(50),
     "sensorname" character varying,
     "imagebits" character varying,
     "coolermode" character varying,
     "lowcoolert" character varying(10),
     "maxexposuretime" character varying(100),
     "minexposuretime" character varying(100),
     "exposuretimeration" character varying(50),
     "fullwelldepth" character varying(50),
     "readoutmode" character varying,
     "readoutspeed" character varying,
     "transferspeed" character varying,
     "gainmode" character varying,
     "gainnumber" character varying,
     "gain_noise" TEXT,
     "shuttertype" character varying,
     "shuttermode" character varying,
     "issupportfullframe" character varying(5),
     "issupportem" character varying(5),
     "issupportscmosnoisefilter" character varying(5),
     "issupportbaseline" character varying(5),
     "issupportoverscan" character varying(5),
     "bin" character varying,
     "issupportroi" character varying(5),
     "interfacetype" character varying,
     "exposetriggermode" character varying,
     "emmaxvalue" character varying(50),
     "emminvalue" character varying(50),
     "canconnect" character varying(5),
     "cansetcoolert" character varying(5),
     "cansetexposureparam" character varying(5),
     "canstartexposure" character varying(5),
     "canstopexposure" character varying(5),
     "canabortexposure" character varying(5),
     "cansetgain" character varying(5),
     "cansetreadoutspeedmode" character varying(5),
     "cansettransferspeedmode" character varying(5),
     "cansetbin" character varying(5),
     "cansetroi" character varying(5),
     "cansetshutter" character varying(5),
     "cansetfullframe" character varying(5),
     "cansetem" character varying(5),
     "cannoisefilter" character varying(5),
     "cansetbaseline" character varying(5),
     "cansetoverscan" character varying(5),
     "attrversion" character varying(5),
     "attrmodifytime" character varying(30)
);/*ccd的固定属性表 建表sql 结束*/

/*滤光片的固定属性表 建表sql*/ 
CREATE TABLE filterconf(
     "id" serial PRIMARY KEY,
	 "filterid" character varying(20)NOT NULL, /*滤光片 id*/
	 "ip" character varying(200),
	 "name" character varying(200) NOT NULL,
     "teleid" character varying(10) NOT NULL, /*对应atlist表中的id字段*/
     "numberoffilter" character varying(30),
     "filtersystem" character varying,
     "filtersize" character varying,
     "filtershape" character varying,
     "cansetfilterposition" character varying(10),
     "canconnect" character varying(10),
     "canfindhome" character varying(10),
     "attrmodifytime" character varying(30),
     "attrversion" character varying(10)
);/*滤光片的固定属性表 建表sql 结束*/

/*随动式圆顶的固定属性表 建表sql*/ 
CREATE TABLE sdomeconf(
     "id" serial PRIMARY KEY,
	 "sdomeid" character varying(20)NOT NULL, /*随动圆顶 id*/
	 "ip" character varying(200),
	 "name" character varying(200) NOT NULL,
     "teleid" character varying(10) NOT NULL, /*对应atlist表中的id字段*/ 
     "dometype" character varying,
     "hasshade" character varying(10),
     "maxspeed" character varying(10),
     "diameter" character varying(50),
     "cansetdomepositin" character varying(10),
     "cansetshadeposition" character varying(10),
     "cansetrotatespeed" character varying(10),
     "canstop" character varying(10),
     "canopenshutter" character varying(10),
     "cansetshadespeed" character varying(10),
     "canconnect" character varying(10),
     "attrmodifytime" character varying(50),
     "attrversion" character varying(10)
);/*随动式圆顶的固定属性表 建表sql 结束*/ 


/*全开圆顶的固定属性表 建表sql*/ 
CREATE TABLE odomeconf(
     "id" serial PRIMARY KEY,
	 "odomeid" character varying(20)NOT NULL, /*全开圆顶 id*/
	 "ip" character varying(200),
	 "name" character varying(200) NOT NULL,
     "teleid" character varying(10) NOT NULL, /*对应atlist表中的id字段*/
     "type" character varying,
     "diameter" character varying(20),
     "canopendome" character varying(10),
     "canconnect" character varying(10),
     "attrversion" character varying(10),
     "attrmodifytime" character varying(20)
);/*全开圆顶的固定属性表 建表sql 结束*/

/*调焦器的固定属性表 建表sql*/ 
CREATE TABLE focusconf(
     "id" serial PRIMARY KEY,
	 "focusid" character varying(20)NOT NULL, /*调焦器 id*/
	 "ip" character varying(200),
	 "name" character varying(200) NOT NULL,
     "teleid" character varying(10) NOT NULL, /*对应atlist表中的id字段*/
     "maxvalue" character varying(20),
     "minvalue" character varying(20),
     "increment" character varying(20),
     "maxspeed" character varying(20),
     "canfindhome" character varying(10),
     "cantemperturecompensate" character varying(10),
     "canconnect" character varying(10),
     "cansetposition" character varying(10),
     "cansetspeed" character varying(10),
     "canstop" character varying(10),
     "canenabletemperturecompensate" character varying(10),
     "cansettemperturecompensatecoefficient" character varying(10),
     "attrversion" character varying(10),
     "attrmodifytime" character varying(20)
);/*调焦器的固定属性表 建表sql 结束*/

/*导星望远镜固定属性表 建表sql*/ 
CREATE TABLE guideconf(
     "id" serial PRIMARY KEY,
	 "guidescopeid" character varying(20)NOT NULL, /*导星望远镜 id*/
	 "ip" character varying(200),
	 "name" character varying(200) NOT NULL,
     "teleid" character varying(10) NOT NULL, /*对应atlist表中的id字段*/
	 "aperture" character varying(20),
	 "focuslength" character varying,
	 "hasmirrorcover" character varying(10),
	 "issupportautofocus" character varying(10),
	 "canconnect" character varying(10),
	 "canopencover" character varying(10),
	 "canenableautofocus" character varying(10),
     "attrversion" character varying(10),
     "attrmodifytime" character varying(20)
);/*导星望远镜固定属性表 建表sql 结束*/
 
 /*动态增减的19个固定属性表*/ 
CREATE TABLE confoption(
     "id" serial PRIMARY KEY,
     "conf" character varying(200) NOT NULL,
     "conf_val" character varying NOT NULL
);/*动态增减的19个固定属性表*/ 

 /*动态增减的19个固定属性表*/ 
CREATE TABLE plandata(
     "id" serial PRIMARY KEY,
     "atuser" character varying(100),/*执行计划的用户*/
     "at" character varying(200),/*执行计划的望远镜*/
     "plan" character varying/*计划的数组转为json后的字串*/
);/*动态增减的19个固定属性表*/

 /*存储各望远镜自设备的IP id 设备名称 判断IP id 名称是否重复*/ 
CREATE TABLE devipid(
     "id" serial PRIMARY KEY,
     "teleid" character varying(20),/*对应atlist表中的id字段*/
     "dev" character varying(100), /*设备名称（如 gimbal,ccd）*/
	 "devnum" character varying(100), /*设备序号（用于区别同类的多个设备：如：1表示1号ccd）*/
     "ip" character varying(200),/*设备ip地址*/
     "devid" character varying,/*设备id*/
     "devname" character varying/*设备名称*/
);/*存储各望远镜自设备的IP id 设备名称*/

CREATE TABLE plancooper (/*协同计划表*/
  "id" BIGSERIAL NOT NULL PRIMARY KEY,
  "user" character varying,
  "project" TEXT NOT NULL,
  "target" TEXT NOT NULL,
  "filter" TEXT NOT NULL,
  "at" INTEGER NOT NULL,
  "type" INTEGER NOT NULL,
  "epoch" INTEGER NOT NULL,
  "exposureCount" INTEGER NOT NULL,
  "gain" INTEGER NOT NULL,
  "bin" INTEGER NOT NULL,
  "readout" INTEGER NOT NULL,
  "exposureTime" INTEGER NOT NULL,
  "rightAscension" INTEGER NOT NULL,
  "declination" INTEGER NOT NULL,
  "delayTime" INTEGER NOT NULL,
  "atname" character varying NOT NULL,
  "import" character varying NOT NULL);

  CREATE TABLE plantoo (/*ToO计划表*/
  "id" BIGSERIAL NOT NULL PRIMARY KEY,
  "user" character varying,
  "project" TEXT NOT NULL,
  "target" TEXT NOT NULL,
  "filter" TEXT NOT NULL,
  "at" INTEGER NOT NULL,
  "type" INTEGER NOT NULL,
  "epoch" INTEGER NOT NULL,
  "exposureCount" INTEGER NOT NULL,
  "gain" INTEGER NOT NULL,
  "bin" INTEGER NOT NULL,
  "readout" INTEGER NOT NULL,
  "exposureTime" INTEGER NOT NULL,
  "rightAscension" INTEGER NOT NULL,
  "declination" INTEGER NOT NULL,
  "delayTime" INTEGER NOT NULL,
  "atname" character varying NOT NULL,
  "import" character varying NOT NULL);

"C:\Program Files (x86)\pgAdmin 4\v1\runtime\pgAdmin4.exe"   //执行 postgres命令行