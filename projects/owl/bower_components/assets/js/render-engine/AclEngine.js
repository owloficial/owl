var AclEngine = function() {
    var READ_ONLY = "R";
    var INVISIBLE = "I";
 
    /** 
     * This block will be used just in cases of the component is a field. It happens because when
     * a field is invisible, its relative grid column must also be invisible.
     *
     * @param component
     * @param componentName
     */
     var setRulesToGridColumns = function(component, componentName) {
        if (component.grid) {
            var column = component.grid.columns.filter(function(item) {
                if (item.name === componentName) {
                    return item;
                 }
            });
            var field = component.fields.filter(function(item) {
                if (item.name === componentName) {
                    return item;
                }
            });
            column[0].isVisible = field[0].isVisible;
            column[0].readOnly  = field[0].readOnly;
        }
    };

    /**
     * This method is used to set the rules of a father component to all of its children.
     *
     * @param children
     * @param component
     */
    var propagateToChildren = function(children, component) {
        if (children) {
            for (var i in children) {
                // Code suggested by sonar. Its intention is to avoid foreach
                // to work in non desired attributes (like that ones inheritance).
                if (children.hasOwnProperty(i)) {
                    children[i].isVisible  = component.isVisible;
                    children[i].readOnly = component.readOnly;
                }
            }
        }
    };

    /**
     * Auxiliary method for propagate rules of widgets. It will set the rules of a widget to its fields and actions.
     *
     * @param component
     */
    var propagateWidgetRules = function(component) {
        if (component.fields) {
            propagateToChildren(component.fields, component);
        }

        if (component.actions) {
            propagateToChildren(component.actions, component);
        }
    };

    /**
     * Auxiliary method for propagate rules of menus. It will set the rules of a menu to its children menus.
     *
     * @param component
     */
    var propagateMenuRules = function(component) {
        for (var i = 0; i < component.menus.length; i++) {
            component.menus[i].isVisible = component.isVisible;
            component.menus[i].readOnly  = component.readOnly;
        }
    };

    /**
     * This method is used to propagate the component's acl rules to its children. For example, if a widget has
     * readOnly = true, then all of its children (all fields and actions), will have the same behavior, since it
     * does not have its own rules.
     *
     * @param component
     */
    var propagateRules = function(component) {
        // If a component has widgets, so it is a container. If it has fields or actions, so it is a widget.
        // If nothing of this attributes exists, then doesn't matter what it is (nothing will be done).
        if (component.menus) {
            propagateMenuRules(component);
        } else if (component.widgets) {
            propagateContainerRules(component);
        } else if (component.fields || component.actions) {
            propagateWidgetRules(component);
        }
    };

    /**
     * Auxiliary method for propagate rules of components. It will set the rules of a container to its widgets.
     * This method have to be recursive because an ACL rule of a grandfather must be inherited by a grandchild
     * component.
     *
     * @param component
     */
    var propagateContainerRules = function(component) {
        for (var i = 0; i < component.widgets.length; i++) {
            component.widgets[i].isVisible  = component.isVisible;
            component.widgets[i].readOnly = component.readOnly;
            propagateRules(component.widgets[i]);
        }
    };

    /**
     * This method is used to propagate the acl rules from a child node to a parent node. This is used because
     * a parent node can get a kind of acl rule just to propagate to its children. If there's a children of it
     * with some rules, that rules must be used. So, the parent's rules must be changed.
     * Notice that, the rules have a certain kind of priority, where, if a child node has a more permissive
     * set of rules, the father node will also use this set rules.
     *
     * @param component
     * @param parentComponent
     */
    var upPropagation = function (component, parentComponent) {
        if (parentComponent !== null && component.isVisible) {
            parentComponent.readOnly  = false;
            parentComponent.isVisible = true;
        }
    };

    /**
     * This method will modify the rules of a component.
     *
     * @param rules     Set of rules that will be used in this component.
     * @param component Component that will have its rules changed.
     * @param userId    User Identifier.
     */
    var setAclRules = function (rules, component, userId) {
        var rule             = rules && rules.userGroups[userId].rule;
        // A new set of rules will be applied only if there's new ones to be set or if there isn't rules set to
        // this component. In this latter case, the default configuration will be set (ACL = "Allow All").
        if (rule || component.readOnly === undefined) {
            // If there is a directive set for component = readOnly, then the component will assume this state
            // (readOnly).
            component.readOnly = (rule === READ_ONLY);
            // If there is no directives set for component or the rule is different of invisible ("I"), then the
            // component will be shown. Otherwise, this will be hide.
            component.isVisible = (rule === undefined || rule !== INVISIBLE);
        }

    };

    /**
     * Process the ACL rules for menu.
     * Those rules are used separated from the others because they are proceeded when the first window is open (the
     * other ones will be used every time its windows are open).
     *
     * @param menu        menu's constructor object.
     * @param parentMenu  Parent's menu.
     * @param aclRules    Object containing all acl rules set to this project.
     * @param containers  Object containing all containers. This object will be used when a menu is set as read only.
     * @param userGroupId User identifier. This value is used to get the right set of rules (The rules are created for
     *                        group of users).
     */
    this.processMenuAcl = function (menu, parentMenu, aclRules, containers, userGroupId) {
        if (aclRules) {
            var menuName = menu.name;
            var rule     = aclRules[menuName];
            setAclRules(rule, menu, userGroupId);
            if (menu.readOnly) {
                containers[menu.windowName].isAccessible = false;
            }

            if (menu.hasChild && menu.menus) {
                propagateRules(menu);
                for (var i in menu.menus) {
                    if (menu.menus.hasOwnProperty(i)) {
                        this.processMenuAcl(menu.menus[i], menu, aclRules, containers, userGroupId);
                    }
                }
            }

            upPropagation(menu, parentMenu);
        }
    };

    /**
     * This method will return the component's children.
     *
     * @param component parent component.
     */
    var getComponentsChildren = function(component) {
        if (component.widgets) {
            return component.widgets;
        } else if (component.fields || component.actions) {
            return component.fields.concat(component.actions);
        }
    };

    /**
     * Process the ACL rules for components under container.
     * This method will process, recursively, all component under a certain container (here, component is assumed as all
     * kind of object, like container, widget, field or action) and set its respective set of Access rules.
     * It's important to notice that a set ACL rule here uses inheritance to determine the rules of one component's
     * child, since this child don't have any ACL rule. For example, the rules set to a container can be used in a
     * widget or its children, since any of this doesn't have its own set of rules.
     * In the same way, if a child have a rule more permissive than its parent node component, this parent node will
     * change its rules, allowing the node's rules work (here, it was called "up propagation"). For example, if a widget
     * has rules allowing read and write and its parent container has rules of "invisible", this container must use,
     * then, the same rules as its child, the widget. Nonetheless, all other children of this container, if don't have
     * rules, will inherit the original rules of container ("invisible").
     * The ACL configuration was thought to create three kinds of rules: Allow (allowing the component to be read and
     * write), "read only" (allowing just reading for this component) and "invisible" (denying reading and writing this
     * component).
     *
     * @param component       Current component. This is which component will suffer the ACL validation.
     * @param parentComponent Component's father. This object will be used to allow the "up propagation" of rules.
     * @param aclRules        Object containing all acl rules set to this project.
     * @param userGroupId     User identifier. This value is used to get the right set of rules (The rules are created
     *                        for group of users).
     */
    this.processAcl = function (component, parentComponent, aclRules, userGroupId) {
        var componentName    = component.name;
        var rules            = aclRules[componentName];

        setAclRules(rules, component, userGroupId);

        propagateRules(component);
        var children = getComponentsChildren(component);
        if (children) {
            for (var i in children) {
                if (aclRules[children[i].name]) {
                    this.processAcl(children[i], component, aclRules, userGroupId);
                }
            }
        }

        upPropagation(component, parentComponent);
        // This block will be used just in cases of the component is a field. It happens because when
        // a field is invisible, its relative grid column must also be invisible.
        if (component.template && component.template.indexOf("field") >= 0) {
            setRulesToGridColumns(parentComponent, componentName);
        }
    };

};
