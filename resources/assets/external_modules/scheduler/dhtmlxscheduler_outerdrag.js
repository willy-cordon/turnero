/*
@license
dhtmlxScheduler v.4.3.35 Professional Evaluation

This software is covered by DHTMLX Evaluation License. Contact sales@dhtmlx.com to get Commercial or Enterprise license. Usage without proper license is prohibited.

(c) Dinamenta, UAB.
*/
Scheduler.plugin(function(e){e.attachEvent("onTemplatesReady",function(){function t(t,n,i,r){if(!e.checkEvent("onBeforeExternalDragIn")||e.callEvent("onBeforeExternalDragIn",[t,n,i,r,a])){var s=e.attachEvent("onEventCreated",function(n){e.callEvent("onExternalDragIn",[n,t,a])||(this._drag_mode=this._drag_id=null,this.deleteEvent(n))}),d=e.getActionData(a),_={start_date:new Date(d.date)};if(e.matrix&&e.matrix[e._mode]){var o=e.matrix[e._mode];_[o.y_property]=d.section;var l=e._locate_cell_timeline(a);
_.start_date=o._trace_x[l.x],_.end_date=e.date.add(_.start_date,o.x_step,o.x_unit)}e._props&&e._props[e._mode]&&(_[e._props[e._mode].map_to]=d.section),e.addEventNow(_),e.detachEvent(s)}}var a,n=new dhtmlDragAndDropObject,i=n.stopDrag;n.stopDrag=function(e){return a=e||event,i.apply(this,arguments)},n.addDragLanding(e._els.dhx_cal_data[0],{_drag:function(e,a,n,i){t(e,a,n,i)},_dragIn:function(e,t){return e},_dragOut:function(e){return this}}),dhtmlx.DragControl&&dhtmlx.DragControl.addDrop(e._els.dhx_cal_data[0],{
onDrop:function(e,n,i,r){var s=dhtmlx.DragControl.getMaster(e);a=r,t(e,s,n,r.target||r.srcElement)},onDragIn:function(e,t,a){return t}},!0)})});