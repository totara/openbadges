// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Script for managing badge criteria.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */
M.core_badges = {
    init: function(Y, options) {
        var CriteriaHelper = function(args) {
            CriteriaHelper.superclass.constructor.apply(this, arguments);
        };
        CriteriaHelper.NAME = "CRITERIA";
        CriteriaHelper.ATTRS = {
            options: {},
        };
        Y.extend(CriteriaHelper, Y.Base, {
            api: M.cfg.wwwroot+'/badges/criteria_ajax.php',
            initializer: function(args) {
                var scope = this;
                this.badgeid = args.badgeid;
                scope.register_delete_buttons();
                var dialog = scope.register_dialog();

                var handle = Y.one('#add-criteria');
                if (handle) {
                    handle.on('click', function(e) {
                        e.preventDefault();
                        dialog.show();
                        return false;
                    }, this);
                }
            }, 
            register_dialog: function() {
                var scope = this;
                var dialog = new Y.Panel({
                    srcNode      : '#potential-criteria-list',//Y.Node.create('<div id="dialogpanel" />'),
                    headerContent: 'Available criteria',
                    width        : 410,
                    zIndex     : 6,
                    centered   : true,
                    modal      : true,
                    render     : true,
                    visible    : false,
                    buttons    : {
                        footer: [
                                 {
                                     name   : 'cancel',
                                     label  : 'Cancel',
                                     action : 'onCancel'
                                 },
                                 {
                                     name   : 'proceed',
                                     label  : 'OK',
                                     action : 'onOK'
                                 }
                        ]
                    }
                });

                dialog.onCancel = function (e) {
                    e.preventDefault();
                    this.hide();
                    this.callback = false;
                };

                dialog.onOK = function (e) {
                    e.preventDefault();
                    this.hide();
                    if(this.callback) {
                        this.callback();
                    }
                    this.callback = false;
                };

                var takeAction = function(){
                    scope.request({badgeid: scope.badgeid, criteriatype: 1 }, true);
                };
                dialog.callback = takeAction;

                return dialog;
            },
	            /*
	            post: function() {
	                var ta = Y.one('#dlg-content-'+this.client_id);
	                var scope = this;
	                var value = ta.get('value');
	                if (value && value != M.str.moodle.addcomment) {
	                    var params = {'content': value};
	                    this.request({
	                        action: 'add',
	                        scope: scope,
	                        params: params,
	                        callback: function(id, obj, args) {
	                            var scope = args.scope;
	                            var cid = scope.client_id;
	                            var ta = Y.one('#dlg-content-'+cid);
	                            ta.set('value', '');
	                            var container = Y.one('#comment-list-'+cid);
	                            var result = scope.render([obj], true);
	                            var newcomment = Y.Node.create(result.html);
	                            container.appendChild(newcomment);
	                            var ids = result.ids;
	                            var linktext = Y.one('#comment-link-text-'+cid);
	                            if (linktext) {
	                                linktext.set('innerHTML', M.str.moodle.comments + ' ('+obj.count+')');
	                            }
	                            for(var i in ids) {
	                                var attributes = {
	                                    color: { to: '#06e' },
	                                    backgroundColor: { to: '#FFE390' }
	                                };
	                                var anim = new Y.YUI2.util.ColorAnim(ids[i], attributes);
	                                anim.animate();
	                            }
	                            scope.register_delete_buttons();
	                        }
	                    }, true);
	                } else {
	                    var attributes = {
	                        backgroundColor: { from: '#FFE390', to:'#FFFFFF' }
	                    };
	                    var anim = new Y.YUI2.util.ColorAnim('dlg-content-'+cid, attributes);
	                    anim.animate();
	                }
	            }, */
	            request: function(args, noloading) {
	                var params = {};
	                var scope = this;
	                params['badgeid']      = args.badgeid;
	                params['criteriatype'] = args.criteriatype;
	                //params['paramid']      = args.paramid;
	                var cfg = {
	                    method: 'GET',
	                    on: {
	                        complete: function(id,o,p) {
	                            if (!o) {
	                                alert('IO FATAL');
	                                return false;
	                            }
	                            var data = o.responseText;
	                            var current = Y.one('#current-criteria-body');
	                            current.appendChild(Y.Node.create(data));
	                            var potential = Y.one('#potential-criteria-body');
	                            if (potential.get('children').size() == current.get('children').size())
	                            	Y.one('#add-criteria').setStyle('display', 'none');;
                                //args.callback(id,data,p);
	                            return true;
	                        }
	                    },
	                    arguments: {
	                        scope: scope
	                    },
	                    headers: {
	                        'Content-Type': 'text/html'
	                    },
	                    data: build_querystring(params)
	                };
	                Y.io(this.api, cfg);
	                if (!noloading) {
	                    this.wait();
	                }
	            }, /*
	            load: function() {
	                var scope = this;
	                var container = Y.one('#comment-ctrl-'+this.client_id);
	                var params = {
	                    'action': 'get',
	                    'page': page
	                };
	                this.request({
	                    scope: scope,
	                    params: params,
	                    callback: function(id, ret, args) {
	                        var linktext = Y.one('#comment-link-text-'+scope.client_id);
	                        if (ret.count && linktext) {
	                            linktext.set('innerHTML', M.str.moodle.comments + ' ('+ret.count+')');
	                        }
	                        container = Y.one('#comment-list-'+scope.client_id);
                            var result = scope.render(ret.list);
	                        container.set('innerHTML', result.html);
	                        var img = Y.one('#comment-img-'+scope.client_id);
	                        if (img) {
	                            img.set('src', M.util.image_url('t/expanded', 'core'));
	                        }
	                        args.scope.register_delete_buttons();
	                    }
	                });
	            },*/
	            register_delete_buttons: function() {
	                var scope = this;
	                Y.all('div.comment-delete a').each(
	                    function(node, id) {
	                        Y.Event.purgeElement('#' + node.get('id'), false, 'click');
	                        node.on('click', function(e, node) {
	                            e.preventDefault();
	                            var theid = this.get('id');
	                            var parseid = theid.replace("remove-", "");
	                            
	                            var removing = new Y.Anim({
	                                node: "#" + parseid,
	                                to: {opacity: 0},
	                                easing: Y.Easing.easeOut,
	                                duration: 0.5
	                            });
	                            removing.on('end', function() {
	                                var toremove = Y.one(removing.get('node'));
	                                toremove.remove(); 
	                            });
	                            removing.run();
	                        });
	                    }
	                );
	            },
	            wait: function() {
	                var container = Y.one('#current-criteria-body');
	                container.set('innerHTML', '<div class="mdl-align"><img src="' + M.util.image_url('i/loading_small', 'core') + '" /></div>');
	            }
	            });


        new CriteriaHelper(options);
    }
};