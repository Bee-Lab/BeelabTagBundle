BeelabTagBundle Documentation
=============================

## Installation

1. [Install BeelabTagBundle](#1-install-beelabtagbundle)
2. [Configuration](#3-configuration)

### 1. Install BeelabTagBundle

Run from terminal:

```bash
$ php composer.phar require beelab/tag-bundle:1.*
```

Enable bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Beelab\TagBundle\BeelabTagBundle(),
    );
}
```

### 2. Configuration

Create a ``Tag`` entity class.
Example:

```php
<?php
// src/Acme/DemoBundle/Entity

namespace Acme\DemoBundle\Entity;

use Beelab\TagBundle\Tag\TagInterface 
use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity()
 */
class Tag implements TagInterface
{
    
}
```

Insert in main configuration:

```yaml
# app/config/config.yml

# BeelabTag Configuration
beelab_tag:
    tag: Acme\DemoBundle\Entity\Tag
```

Then you can create some entities that implement TaggableInteface.
