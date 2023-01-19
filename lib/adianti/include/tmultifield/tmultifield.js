/**
 * TMultiField
 * Thiago Schimuneck
 * Pablo Dall'Oglio
 */
function MultiField(objId,width,height)
{
    // Configurações
    this.formFieldsName = Array();
    this.formFieldsMandatory = Array();
    this.formFieldsAlias = Array();
    this.formPostFields = Array();
    this.storeButton = null;
    this.deleteButton = null;
    this.cancelButton = null;
    this.inputResult = null;

    this.addEndCol = function (obj)
    {
        if(document.all)return;
        var rows = obj.getElementsByTagName('TR');
        for(var no=0;no<rows.length;no++)
        {
            var cell = rows[no].insertCell(-1);
            cell.innerHTML = '&nbsp;';
            cell.style.width = '13px';
            cell.width = '13';
        }	
    }

    this.initmultifield = function (objId,width,height)
    {
        width = width + '';
        height = height + '';
        var obj = document.getElementById(objId);
        this.mtf = obj;
        obj.parentNode.className='multifieldDiv';
        var self = this;
        if(navigator.userAgent.indexOf('MSIE')>=0)
        {
            obj.parentNode.style.overflowY = 'auto';
        }
        if(width.indexOf('%')>=0)
        {
            obj.style.width = width;
            obj.parentNode.style.width = width;
        }
        else
        {
            obj.style.width = width + 'px';
            obj.parentNode.style.width = width + 'px';
        }

        if(height.indexOf('%')>=0)
        {
            obj.parentNode.style.height = height;			
        }
        else
        {
            obj.parentNode.style.height = height + 'px';
        }
        this.addEndCol(obj);		
        obj.cellSpacing = 0;
        obj.cellPadding = 0;
        obj.className='multifield';
        var tHead = obj.getElementsByTagName('THEAD')[0];
        var cells = tHead.getElementsByTagName('TD');
        for(var no=0;no<cells.length;no++)
        {
            cells[no].className = 'multifield_header';
        }		
        var tBody = obj.getElementsByTagName('TBODY')[0];
        for(no=0;no<tBody.rows.length;no++)
        {
            tBody.rows[no].onmousedown = function(){self.highlightSelectedRow(this);self.dragDropStart(this);return false;};
            tBody.rows[no].onmouseup = function(){self.dragDropStop(this);return false;};
            tBody.rows[no].onselectstart = function () { return false; }; // Para o I.E.
            var row = tBody.rows[no];
            for(i=0;i<row.cells.length;i++)
                row.cells[i].className='multifieldtd';
        }
        if(document.all && navigator.userAgent.indexOf('Opera')<0)
        {
            tBody.className='tmultifield_scrolling';
            tBody.style.display='block';			
        }
        else
        {
            tBody.className='tmultifield_scrolling';
            tBody.style.height = (obj.parentNode.clientHeight-tHead.offsetHeight) + 'px';
            if(navigator.userAgent.indexOf('Opera')>=0)
            {
                obj.parentNode.style.overflow = 'auto';
            }
        }

        for(var no=1;no<obj.rows.length;no++)
        {
            obj.rows[no].onmouseover = this.highlightDataRow;
            obj.rows[no].onmouseout = this.deHighlightDataRow;
        }		
    }

    /**
     * Chamado quando comeca a atualizar a tabela
     */
    this.dragDropStart = function (row)
    {
        this.dragRow = row;
    }
    
    /**
     * Chamado quando termina de atualizar a tabela
     */
    this.dragDropStop = function (row)
    {
        if ((this.dragRow) && (row != this.dragRow))
        {
            var auxAId = row.getAttribute('dbId');
            var auxBId = this.dragRow.getAttribute('dbId');
            this.dragRow.setAttribute('dbId',auxAId);
            row.setAttribute('dbId',auxBId);
            var tmp = document.createElement('TR');
            this.mtf.tBodies[0].appendChild(tmp);
            /* Tem que fazer este monte de trampa e passar célula por célula por compatibilidade com o I.E.*/
            for(var x=0;x<row.cells.length;x++)
            {
                var td = document.createElement('TD');
                td.innerHTML = row.cells[x].innerHTML;
                tmp.appendChild(td);
                row.cells[x].innerHTML=this.dragRow.cells[x].innerHTML;
                row.cells[x].className='multifieldtd';
            }
            for(var x=0;x<tmp.cells.length;x++)
            {
                this.dragRow.cells[x].innerHTML = tmp.cells[x].innerHTML;
                this.dragRow.cells[x].className='multifieldtd';

            }
            this.mtf.tBodies[0].removeChild(tmp);
            this.dragRow.style.background='';
            this.mtf.style.cursor = '';
            row.onmousedown();
            this.dragRow = null;
        }
    }

    this.highlightDataRow = function()
    {
        if(navigator.userAgent.indexOf('Opera')>=0)return;
        this.className='tmultifield_over';
        if(document.all)
        {	// I.E fix for "jumping" headings
            var divObj = this.parentNode.parentNode.parentNode;
            var tHead = divObj.getElementsByTagName('TR')[0];
            tHead.style.top = divObj.scrollTop + 'px';
        }	
    }

    this.deHighlightDataRow = function ()
    {
        if(navigator.userAgent.indexOf('Opera')>=0)return;
        this.className=null;
        if(document.all)
        {	// I.E fix for "jumping" headings
            var divObj = this.parentNode.parentNode.parentNode;
            var tHead = divObj.getElementsByTagName('TR')[0];
            tHead.style.top = divObj.scrollTop + 'px';
        }			
    }

    this.highlightSelectedRow = function(row)
    {
        if(navigator.userAgent.indexOf('Opera')>=0)return;
        if (this.selectedRow != null)
        {
            this.selectedRow.style.backgroundColor = '';
        }
        //alert(row.style);
        row.style.backgroundColor = '#88FF88';
        //row.style.setProperty("background-color",'#88FF88', 'important');
        this.selectedRow = row;
        var x;
        for(x=0;x<this.formFieldsName.length;x++)
        {
            var field = document.getElementsByName(this.formFieldsName[x])[0];
            if (field.type != 'select-one')
            {
                field.value = row.cells[x].innerHTML;                
            }
            if (typeof field.onchange == "function") field.onchange();
/*            if (field)
            {
                // aqui, trampa, para funcionar combo, faz um split sempre por '::'
		vetor = row.cells[x].innerHTML.split('::');
		field.value = vetor[0];
            }*/
        }
        var self = this;
        if (this.storeButton)
        {
//            this.storeButton.value='Atualizar';
            this.storeButton.onclick = function () {
                self.updateRowValuesFromFormFields(row);
                self.unselectRow();
                if(self.cancelButton)self.cancelButton.setAttribute('disabled','1');
                if(self.deleteButton)self.deleteButton.setAttribute('disabled','1');
                this.onclick = function() { self.addRowFromFormFields(); };
//                this.value = 'Adicionar';
            }
        }
        if (this.deleteButton)
        {
            this.deleteButton.removeAttribute('disabled');
            this.deleteButton.onclick = function () {
                self.deleteRow();
                if(self.cancelButton)self.cancelButton.setAttribute('disabled','1');
                if(self.deleteButton)self.deleteButton.setAttribute('disabled','1');
                if(self.storeButton)
                {
                    self.storeButton.onclick = function() { self.addRowFromFormFields(); };
//                    self.storeButton.value = 'Adicionar';
                }
            };
        }
        if (this.cancelButton)
        {
            this.cancelButton.removeAttribute('disabled');
            this.cancelButton.onclick = function () {
                self.unselectRow();
                if(self.cancelButton)self.cancelButton.setAttribute('disabled','1');
                if(self.deleteButton)self.deleteButton.setAttribute('disabled','1');
                if(self.storeButton)
                {
                    self.storeButton.onclick = function() { self.addRowFromFormFields(); };
//                    self.storeButton.value = 'Adicionar';
                }
            };
        }
    }

    this.addRowFromFormFields = function ()
    {
        var row = document.createElement('TR');
        this.mtf.tBodies[0].appendChild(row);
        var self = this;
        row.onmousedown = function(){self.highlightSelectedRow(this);self.dragDropStart(this);return false;};
        row.onmouseup = function(){self.dragDropStop(this);return false;};
        row.onmouseover = this.highlightDataRow;
        row.onmouseout = this.deHighlightDataRow;
        for(var x=0;x<this.formFieldsName.length;x++)
        {
            var cell = document.createElement('TD');
            row.appendChild(cell);
            cell.innerHTML = this.getValueField(this.formFieldsName[x]);
            if (this.ltrim(cell.innerHTML) == '' && this.formFieldsMandatory[x])
            {
                alert ('O campo '+this.mtf.tHead.rows[0].cells[x].innerHTML+' é obrigatório');
                document.getElementsByName(this.formFieldsName[x])[0].focus();
                return false;
            }
        }
        if(!document.all)row.insertCell(-1);
        this.clearFormFields();
        return true;
    }

    this.ltrim = function (str)
    {
        var s = str;
        while(s.substr(0,1) == ' '){s = s.substr(1);}
        return s;
    }

    this.updateRowValuesFromFormFields = function (row)
    {
        var x;
        for(x=0;x<this.formFieldsName.length;x++)
        {
            var v = this.getValueField(this.formFieldsName[x]);
            if (this.ltrim(v) == '' && this.formFieldsMandatory[x])
            {
                alert ('O campo '+this.mtf.tHead.rows[0].cells[x].innerHTML+' é obrigatório');
                document.getElementsByName(this.formFieldsName[x])[0].focus();
                return false;
            }
            row.cells[x].innerHTML = v;
        }
        this.clearFormFields();
        return true;
    }

    this.unselectRow = function(row)
    {
        var _uns = function(r){
            r.onmouseout();
            r.style.backgroundColor = '';
        }
        if ( row )
        {
            _uns(row);
        }
        else if (this.selectedRow)
        {
            _uns(this.selectedRow);
            this.clearFormFields();
        }
    }

    this.deleteRow = function ()
    {
        if (this.selectedRow)
        {
            var row = this.selectedRow;
            this.unselectRow();
            this.mtf.tBodies[0].removeChild(row);
        }
    }

    this.clearFormFields = function ()
    {
        for(x=0;x<this.formFieldsName.length;x++)
        {
            var inputs = document.getElementsByName(this.formFieldsName[x]);
            switch(inputs[0].type)
            {
                case 'text':    inputs[0].value='';
                                break;
                case 'radio':   for(var y=0;y<inputs.length;y++){inputs[y].checked = false;}
                                break;
                case 'select-one': inputs[0].selectedIndex=-1;try{inputs[0].value=0;}catch(e){}
                                   break;
                case 'select-multiple': for(var y=0;y<inputs[0].options.length;y++){
                                            inputs[0].options[y].selected = false;}
                                        break;
                case 'checkbox': inputs[0].checked =false;
                                 break;
            }
        }
    }

    this.getValueField = function (field)
    {
        var inputs = document.getElementsByName(field);
        if (inputs)
        {
            switch(inputs[0].type)
            {
                case 'text':    return inputs[0].value;
                                break;
                case 'radio':   for(var y=0;y<inputs.length;y++){if(inputs[y].checked)return inputs[y].value;}
                                break;
                case 'select-one': if(inputs[0].selectedIndex>-1){if(inputs[0].options[inputs[0].selectedIndex].value=='0'){return '';}else{return inputs[0].options[inputs[0].selectedIndex].text;}};
                //case 'select-one': if(inputs[0].selectedIndex>-1){if(inputs[0].options[inputs[0].selectedIndex].value=='0'){return '';}else{return inputs[0].options[inputs[0].selectedIndex].value;}};
                                   break;
                case 'select-multiple': for(var y=0;y<inputs[0].options.length;y++){
                                            if(inputs[0].options[y].selected){
                                                v+=','+inputs[0].options[y].value;}}v=v.substr(1);if(v){return v};
                                        break;
                case 'checkbox': return inputs[0].checked ? 'Sim' : 'Não';
                                 break;
            }
        }
        return '';
    }

    this.parseTableToJSON = function ()
    {
        var tbody = this.mtf.tBodies[0];
        var head = this.mtf.tHead.rows[0].cells;
        var result = '[';
        for(var row=0;row<tbody.rows.length;row++)
        {
            result += '{';
            var objRow = tbody.rows[row];
            var max = objRow.cells.length;
            if(!document.all)max = objRow.cells.length-1;
            var values = '';
            for(var col=0;col<max;col++)
            {
                var objCell = objRow.cells[col];
                var content = objCell.innerHTML;//.split('::')[0];
                if (this.formFieldsAlias[col])
                    colname = this.formFieldsAlias[col];
                else
                    colname = head[col].innerHTML;
                if (this.formPostFields[colname])
                {
                    if (values.length>0)
                        values += ',';
                    values += '"' + escape(colname) + '":"' + escape(content) + '"';
                }
            }
            if (objRow.getAttribute('dbId'))
            {
                result += '"id":"'+objRow.getAttribute('dbId')+'"';
                if (values.length>0)
                    result += ','+values;
            }
            else
                result += values;
            result+='}';
            if(row<tbody.rows.length-1)result+=','; 
        }
        result += ']';
        if (this.inputResult)this.inputResult.value = result;
        return result;
    }

    // Construtor
    this.selectedRow = null;
    this.initmultifield (objId,width,height);
}
