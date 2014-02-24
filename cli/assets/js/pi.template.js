    function Template(proto) {
        if (typeof proto === 'string') {
            this.proto = this.fromString(proto);
        } else {
            this.proto = proto.cloneNode(true);
        }
        this.slots = this.findSlots(this.proto);
    }

    Template.prototype.fromString = function(str) {
        var d = document.createDocumentFragment();
        var temp = document.createElement('div');
        temp.innerHTML = str;
        while (temp.firstChild) {
            d.appendChild(temp.firstChild);
        }
        return d;
    };

    Template.prototype.findSlots = function(proto) {
        // textContent slots
        var slots = {};
        var tokens = /^\s*(\w+)\s+(\w+)\s*$/;
        var classes = proto.querySelectorAll('[class]');
        Array.prototype.forEach.call(classes, function(e) {
            var command = ['setText', e];
            Array.prototype.forEach.call(e.classList, function(c) {
                slots[c] = command;
            });
        });
        var attributes = proto.querySelectorAll('[data-attr]');
        Array.prototype.forEach.call(attributes, function(e) {
            var matches = e.getAttribute('data-attr').match(tokens);
            if (matches) {
                slots[matches[1]] = ['setAttr', e, matches[2]];
            }
            e.removeAttribute('data-attr');
        });
        return slots;
    };

    Template.prototype.render = function(data) {
        Object.getOwnPropertyNames(data).forEach(function(name) {
            var cmd = this.slots[name];
            if (cmd) {
                this[cmd[0]].apply(this, cmd.slice(1).concat(data[name]));
            }
        }, this);
        return this.proto.cloneNode(true);
    };

    Template.prototype.setText = (function() {
        var d = document.createElement('div');
        var txtprop = (d.textContent === '') ? 'textContent' : 'innerText';
        d = null;
        return function(elem, val) {
            elem[txtprop] = val;
        };
    }());

    Template.prototype.setAttr = function(elem, attrname, val) {
        elem.setAttribute(attrname, val);
    };



    var tpl = new Template('<p data-attr="cloneid id">This is clone number <span class="clonenumber">one</span>!</p>');

    var tpl_data = {
        cloneid: 0,
        clonenumber: 0
    };
    var df = document.createDocumentFragment();
    for (var i = 0; i < 100; i++) {
        tpl_data.cloneid = 'id' + i;
        tpl_data.clonenumber = i;
        df.appendChild(tpl.render(tpl_data));
    }
    document.body.appendChild(df);