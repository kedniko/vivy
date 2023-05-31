# Vivy

<pre align="center">
🧪 Working in Progress
</pre>

PHP validation engine

🚀 Blazingly fast  
🦾 Type safe  
🎨 Custom validators  
🧩 Plugin first approach  

## Installation

Require this package with composer.

```shell
composer require kedniko/vivy
```

# StandardLibrary Plugin

```mermaid
graph TD
    A[RootType] -->T(Type)
    T --> SCAL(TypeScalar)
    T --> OR(TypeOr)
    T --> COMP(TypeCompound)
    SCAL --> NUM(TypeNumber)
    SCAL --> NULL(TypeNull)
    SCAL --> BOOL(TypeBool)
    NUM --> INT(TypeInt)
    NUM --> FLOAT(TypeFloat)
    T --> ANY(TypeAny)
    T --> UNDEF(TypeUndefined)
    SCAL --> STR(TypeString)
    STR --> FLOATSTR(TypeStringFloat)
    STR --> INTSTR(TypeStringInt)
    STR --> STREMAIL(TypeStringEmail)
    STR --> STRDATE(TypeStringDate)
    SCAL --> STREMPTY(TypeStringEmpty)
    STR --> STRNOTEMPTY(TypeStringNotEmpty)
    COMP --> GR(TypeGroup)
    COMP --> FILE(TypeFile)
    COMP --> FILES(TypeFiles)
    COMP --> ARR(TypeArray)
```
