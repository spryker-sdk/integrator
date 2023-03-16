# Manifest types

The integrator tool is working basing on JSON manifests. It looks in the  directory based on the defined folder structure and takes required manifests from there. File structure must be as shown below.

```
Spryker/                                  <- Organization namespace
    Availability/                         <- Module name
        9.10.0/                           <- Module version
            installer-manifest.json       <- Manifest file
SprykerShop/
    CartPage/
        3.29.0/
            installer-manifest.json
.....
```


For common types, all manifests have the following structure.
```json
{
    "manifest-key-here": [
        {
            "target": "this-is-where-data-needs-to-be-changed-or-where-to-copy-smth",
            "source": "this-is-what-we-want-to-add-remove-copy"
        }
    ]
}
```

 You can find more data for every manifest below.

## Available manifest types

| Type                                                           | Generation | Integration |
|----------------------------------------------------------------|------------|-------------|
| [wire-plugin](#wire-plugin-manifest)                           | YES        | YES         |
| [unwire-plugin](#unwire-plugin-manifest)                       | YES        | YES         |
| [wire-widget](#wire-widget-manifest)                           | YES        | YES         |
| [unwire-widget](#unwire-widget-manifest)                       | YES        | YES         |
| [configure-module](#configure-module-manifest)                 | YES        | YES         |
| [configure-env](#configure-env-manifest)                       | YES        | YES         |
| [copy-module-file](#copy-module-file-manifest)                 | YES        | YES         |
| [wire-glue-relationship](#wire-glue-relationship-manifest)     | YES        | YES         |
| [unwire-glue-relationship](#unwire-glue-relationship-manifest) | YES        | YES         |
| [glossary-key](#glossary-key-manifest)                         | YES        | YES         |
| [add-config-array-element](#add-config-array-element-manifest) | YES        | YES         |
| [wire-navigation](#wire-navigation-manifest)                   | YES        | YES         |
| [unwire-navigation](#unwire-navigation-manifest)               | YES        | YES         |

Generation is currently handled internally in the Spryker release app, whereas integration is done through this code base directly with
[Strategy classes](//github.com/spryker-sdk/integrator/tree/master/src/ManifestStrategy/).

Description of the supported extension scenarios can be found [here](https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/code-upgrader-supported-extension-scenarios.html)

### Wire Plugin Manifest

This manifest type adds a plugin to a required place in the code, by defining its exact method. To add the plugin to an associative array, set the array key in the `index` setting.

To specify where to put the changes, define a position field with the `before` setting or the `after` setting. `before` and `after` accept values which can be both a string and an array of string. The integrator checks the existing `before` and `after` plugins one by one, and puts a value after or before the first find.

The optional `call` section specifies where the target method should be called.

```json
{
    "wire-plugin": [
        {
            "target": "\\Spryker\\Client\\Cart\\CartDependencyProvider::getQuoteStorageStrategyPlugins",
            "source": "\\Spryker\\Client\\Cart\\Plugin\\SessionQuoteStorageStrategyPlugin",
            "index": "\\Pyz\\Client\\Cart\\CartDependencyProvider::PYZ_PLUGIN_KEY",
            "condition": "class_exists(\\Spryker\\Client\\Cart\\Plugin\\SessionQuoteStorageStrategyPlugin::class)",
            "position": {
                "before": "",
                "after": ["", ""]
            },
            "call": {
                "target": "\\Spryker\\Client\\Cart\\CartDependencyProvider::getQuoteStorageStrategyPlugins",
                "after": "",
                "before": ""
            },
            "arguments": {
                "prepend-arguments": [
                    {
                        "value": "",
                        "is_literal": true
                    }
                ],
                "append-arguments": [
                    {
                        "value": "",
                        "is_literal": true
                    }
                ],
                "constructor-arguments": [
                    {
                        "value": "",
                        "is_literal": true
                    }
                ]
            }
        }
    ]
}
```

### Unwire Plugin Manifest

This manifest type removes a Plugin from a specified place of code.

If a plugin should be removed from an associative array, the array key can be stored in `index` setting.

```json
{
    "unwire-plugin": [
        {
            "target": "\\Spryker\\Client\\Cart\\CartDependencyProvider::getQuoteStorageStrategyPlugins",
            "source": "\\Spryker\\Client\\Cart\\Plugin\\SessionQuoteStorageStrategyPlugin",
            "index": "\\Pyz\\Client\\Cart\\CartDependencyProvider::PYZ_PLUGIN_KEY"
        }
    ]
}
```

### Wire Widget Manifest

This manifest adds a Widget class to the SprykerShop.

```json
{
    "wire-widget": [
        {
          "source": "\\SprykerShop\\Yves\\CartPage\\Widget\\ProductAbstractAddToCartButtonWidget"
        }
    ]
}
```

### Unwire Widget Manifest

This manifest removes a Widget class from the SprykerShop.

```json
{
    "unwire-widget": [
        {
          "source": "\\SprykerShop\\Yves\\CartPage\\Widget\\RemoveFromCartFormWidget"
        }
    ]
}
```

### Configure Module Manifest

This manifest adds a constant to a target class specified in the `value` field.

```json
{
    "configure-module": [
        {
            "target": "\\Spryker\\Client\\Catalog\\CatalogConfig::PAGINATION_VALID_ITEMS_PER_PAGE",
            "value": [
                10,
                1000
            ]
        }
    ]
}
```

This manifest sets an expression to a method of a target class specified in the `value` field. All PHP value types are supported.

```json
{
    "configure-module": [
        {
            "target": "\\Spryker\\Client\\Catalog\\CatalogConfig::targetMethod",
            "value": 10
        }
    ]
}
```

This manifest adds expression to specified method of target class. To set value you can specify value field. It can be everything supported by PHP.

```json
{
    "configure-module": [
        {
            "target": "\\Spryker\\Client\\Catalog\\CatalogConfig::targetMethod",
            "value": [
                10
            ]
        }
    ]
}
```

This manifest compares the statements of the target class method and the `previousValue` parameter. If they are the same, the manifest substitutes the expression with the method specified in the `value` field. If they are not the same, the manifest does nothing.

```json
{
    "configure-module": [
        {
            "previousValue": "$variable = 5 + 10; return $variable",
            "target": "\\Spryker\\Client\\Catalog\\CatalogConfig::targetMethod",
            "value": {
                "value": "$variable = 10 + 20; return $variable",
                "is_literal": true
            }
        }
    ]
}
```

### Configure Env Manifest

This manifest adds global constants to the configuration file of the project.

Exact values are set in the `value` field. If it isn't set, Integrator looks for the `choices` field and outputs the request: `Provide value for global configuration`. If you answer with an empty value, Integrator defaults to the `defaultValue` field.

```json
{
    "configure-env": [
        {
            "target": "\\Spryker\\Shared\\GlueApplication\\GlueApplicationConstants::GLUE_APPLICATION_DOMAIN",
            "value": "Value 1 But can be empty (value field is not set)",
            "defaultValue": "Value 1",
            "choices": [
                "Value 1",
                "Value 1"
            ]
        }
    ]
}
```

#### Literal values

Sometimes configs are using not only simple scalar values or constants, but functions or typecasting. In such cases, the config value should look as follows:

```json
{
    "configure-env": [
        {
            "target": "\\Spryker\\Shared\\GlueApplication\\GlueApplicationConstants::GLUE_APPLICATION_DOMAIN",
            "value": {
                "is_literal": true,
                "value": "getenv('APPLICATION_NAME')"
            }
        }
    ]
}
```

### Copy Module File Manifest

This manifest copies a specific file from the `source` to the `target` path.

```json
{
    "copy-module-file": [
        {
            "target": "Zed/SalesReturnSearch/Persistence/Propel/Schema/spy_sales_return_search.schema.xml",
            "source": "data/spy_sales_return_search.schema.xml"
        }
    ]
}
```

### Wire Glue Relationship Manifest

This manifest adds relationship for Glue and internally adds a call of the `addRelationship` method with the parameters from the manifest.

```json
{
    "wire-glue-relationship": [
        {
            "source": {
                "\\Spryker\\Glue\\SalesReturnsRestApi\\SalesReturnsRestApiConfig::RESOURCE_RETURN_ITEMS": "\\Spryker\\Glue\\OrdersRestApi\\Plugin\\OrderItemByResourceIdResourceRelationshipPlugin"
            }
        }
    ]
}
```

### Unwire Glue Relationship Manifest

This manifest removes the Glue relationship from the code.

```json
{
    "unwire-relationship": [
        {
            "source": {
                "\\Spryker\\Glue\\SalesReturnsRestApi\\SalesReturnsRestApiConfig::RESOURCE_RETURN_ITEMS": "\\Spryker\\Glue\\OrdersRestApi\\Plugin\\OrderItemByResourceIdResourceRelationshipPlugin"
            }
        }
    ]
}
```

### Execute Console Manifest

This manifest executes a console command.

```json
{
    "execute-console": [
        {
            "target": {
                "vendor/bin/integrator --dry"
            }
        }
    ]
}
```

### Glossary Key Manifest

This manifest contains glossary keys for the project’s `glossary.yml` file. This mitigates backward compatibility breaking changes, as it allows adding the missing ones, even if they are introduced in minors.

```json
{
    "glossary-key": [
        "new": [
            {
                "cart.shipping.order-item": {
                    "de_DE": "Versand",
                    "en_US": "Shipping"
                }
            }
        ]
    ]
}
```

### Add Config Array Element Manifest

This manifest adds a source constant as an element to the array returned by the target method.

```json
{
    "add-config-array-element": [
        {
            "target": "\\Pyz\\Client\\TestIntegratorAddArrayElement\\TestIntegratorAddArrayElementConfig::getTestConfiguration",
            "value": "\\Spryker\\Shared\\TestIntegrator\\TestIntegratorAddArrayElement::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST"
        },
        {
            "target": "\\Pyz\\Client\\TestIntegratorAddArrayElement\\TestIntegratorAddArrayElementConfig::getTestConfiguration",
            "value": "\\Spryker\\Shared\\TestIntegrator\\TestIntegratorAddArrayElementBefore::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_BEFORE",
            "position": {
                "before": "TestIntegratorAddArrayElement::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST"
            }
        },
        {
            "target": "\\Pyz\\Client\\TestIntegratorAddArrayElement\\TestIntegratorAddArrayElementConfig::getTestConfiguration",
            "value": "\\Spryker\\Shared\\TestIntegrator\\TestIntegratorAddArrayElementAfter::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_AFTER",
            "position": {
                "after": "TestIntegratorAddArrayElement::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST"
            }
        }
    ]
}
```

### Wire Navigation Manifest

This manifest adds a navigation entry to `navigation.xml`.

```json
{
    "wire-navigation": [
        {
            "navigations": {
                "app-catalog-gui": {
                    "label": "Apps",
                    "title": "Apps",
                    "bundle": "app-catalog-gui",
                    "icon": "fa-archive",
                    "module": "app-catalog-gui",
                    "controller": "index",
                    "action": "index"
                }
            },
            "after": "users"
        },
        {
            "navigations": {
                "main": {
                    "pages": {
                        "main-nested": {
                            "bundle": "main",
                            "controller": "index",
                            "action": "index",
                            "visible": "1"
                        }
                    }
                }
            }
        }
    ]
}
```

### Unwire Navigation Manifest

This manifest removes a navigation entry from `navigation.xml`.

```json
{
    "unwire-navigation": [
        {
            "navigations": {
                "delete": null
            }
        },
        {
            "navigations": {
                "catalog": {
                    "pages": {
                        "price-product-schedule": {
                            "icon": null,
                            "pages": {
                                "price-product-schedule-dry-run-import": {
                                    "icon": null
                                },
                                "price-product-schedule-publish": {
                                    "icon": null
                                },
                                "price-product-schedule-list-view": {
                                    "icon": null
                                },
                                "price-product-schedule-list-edit": {
                                    "icon": null
                                },
                                "price-product-schedule-edit": {
                                    "icon": null
                                },
                                "price-product-schedule-list-delete": {
                                    "icon": null
                                }
                            }
                        }
                    }
                }
            }
        }
    ]
}
```

## Suggest new types and functionality

To suggest missing types or functionality, [create an issue](https://github.com/spryker-sdk/integrator/issues). If approved, we will implement them on our side.

Also, feel free to suggest changes via PRs.
