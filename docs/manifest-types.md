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


All manifests have such structure for common types. More data you can find below for every manifest.
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

## Available manifest types


### Wire Plugin Manifest

This manifest type adds Plugin to desired place (by defining exact method) of the code. We can specify the place where to put our changes by specifying position field with before or after settings.

```json
{
    "wire-plugin": [
        {
            "target": "\\Spryker\\Client\\Cart\\CartDependencyProvider::getQuoteStorageStrategyPlugins",
            "source": "\\Spryker\\Client\\Cart\\Plugin\\SessionQuoteStorageStrategyPlugin",
            "position": {
                "before": "",
                "after": ""
            }
        }
    ]
}
```

### Unwire Plugin Manifest

This manifest type removes Plugin from specified place of code.

```json
{
    "unwire-plugin": [
        {
            "target": "\\Spryker\\Client\\Cart\\CartDependencyProvider::getQuoteStorageStrategyPlugins",
            "source": "\\Spryker\\Client\\Cart\\Plugin\\SessionQuoteStorageStrategyPlugin"
        }
    ]
}
```

### Wire Widget Manifest

This manifest adds Widget class to the SprykerShop.

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

This manifest removes Widget class from the SprykerShop.

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

This manifest adds a constant to specified target class. To set value you can specify value field. It can be everything supported by PHP.

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

### Configure Env Manifest

This manifest adds global constants to configuration file of the project.

If we want to give exact value we set value field. If it isn't set then Intergrator looks for `choices` field and ask you a question “Provide value for global configuration“. If you answer with empty value Integrator relies on defaultValue option.

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

### Copy Module File Manifest

This manifest copies Module file from the source to the target path.

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

This manifest adds relationship for Glue and internally adds a call of addRelationship` method with the parameters from manifest.

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

This manifest removes Glue relationship from the code.

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

This manifest executes console command.

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

### [WIP] Glossary Key Manifest

This manifest contains glossary keys for project’s glossary.yml file.This mitigates “BC breaks” as it allows adding those missing ones, even if they are introduced in minors.

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

### [WIP] Add Config Array Element Manifest

This type of manifest adds a source constant as an element to the array returned by the target method.

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

### [WIP] Wire Navigation Manifest

This type of manifest adds a navigation entry into the navigation.xml.

```json
{
    "wire-navigation": [
        {
            "navigations": {
                "app-catalog-gui": {
                    "label": "Apps",
                    "title": "Apps",
                    "icon": "fa-archive",
                    "module": "app-catalog-gui",
                    "controller": "index",
                    "action": "index"
                }
            },
            "after": "users"
        }
    ]
}
```

## Propose new types/functionality

https://github.com/spryker-sdk/integrator/issues
Here you can propose new types or missing functionality regarding manifests.
If approved we will implement them in our side.

You can also open PRs with suggested changes.