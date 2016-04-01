BeelabTagBundle Documentation
=============================

## Installation

1. [Installation](#1-installation)
2. [Configuration](#2-configuration)
3. [Usage](#3-usage)
4. [Other bundles](#4-other-bundles)
5. [Javascript enhancement](#5-javascript-enhancement)

### 1. Installation

Run from terminal:

```bash
$ php composer.phar require beelab/tag-bundle
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
See also [Other bundles](#4-other-bundles) for a note about registering order.

### 2. Configuration

Create a ``Tag`` entity class.
Example:

```php
<?php
// src/AppBundle/Entity
namespace AppBundle\Entity;

use Beelab\TagBundle\Tag\TagInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Tag implements TagInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    protected $name;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
```

Insert in main configuration:

```yaml
# app/config/config.yml

# BeelabTag Configuration
beelab_tag:
    tag_class: AppBundle\Entity\Tag
    purge:     true
```

> **Warning**: the ``purge`` option is not mandatory and defaults to ``false``. You should use this
> option (with ``true`` value) only if you want to delete a tag when a taggable entity
> is deleted. You should avoid purging tags if you configured more than a taggable entity,
> since this could lead to constraint violations.

Then you can create some entities that implement ``TaggableInteface``.

Suppose you want to use tags on an ``Article`` entity. You have two options: implementing ``TaggableInterface``
(more flexible, showed here), or extending ``AbstractTaggable`` (simpler, showed later).

```php
<?php
// src/AppBundle/Entity
namespace AppBundle\Entity;

use Beelab\TagBundle\Tag\TagInterface;
use Beelab\TagBundle\Tag\TaggableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Article implements TaggableInterface
{
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     */
    protected $tags;

    // note: if you generated code with SensioGeneratorBundle, you need
    // to replace "Tag" with "TagInterface" where appropriate

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(TagInterface $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTag(TagInterface $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag(TagInterface $tag)
    {
        return $this->tags->contains($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagNames()
    {
        return empty($this->tagsText) ? array() : array_map('trim', explode(',', $this->tagsText));
    }
}
```

### 3. Usage

Most simple usage is in a Form like this one:

```php
<?php
// src/AppBundle/Form/Type/ArticleFormType
namespace Acme\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
// ...

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // use FQCN here instead of 'text', for Symfony >= 2.8
            ->add('tagsText', 'text', array('required' => false, 'label' => 'Tags'))
            // other fields...
        ;
    }
}
```

Then, add a ``$tagsText`` property to your entity:

```php
<?php
// src/AppBundle/Entity

// use...

class Article implements TaggableInterface
{
    // ...

    protected $tagsText;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    // ...

    /**
     * @param string
     */
    public function setTagsText($tagsText)
    {
        $this->tagsText = $tagsText;
        $this->updated = new \DateTime();
    }

    /**
     * @return string
     */
    public function getTagsText()
    {
        $this->tagsText = implode(', ', $this->tags->toArray());

        return $this->tagsText;
    }

    // ...
}
```

Note that you need to change something in your Entity when ``$tagsText`` is updated,
otherwise flush is not triggered and tags won't work. In example above, we're using
an ``$updated`` DateTime property.

Instead of implementing ``TaggableInterface``, you can extend ``AbstractTag``, like in this example:
```php
<?php
// src/AppBundle/Entity
namespace AppBundle\Entity;

use Beelab\TagBundle\Entity\AbstractTaggable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Article extends AbstractTaggable
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     */
    protected $tags;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @param string $tagsText
     */
    public function setTagsText($tagsText)
    {
        $this->updated = new \DateTime();

        return parent::setTagsText($tagsText);
    }
}
```
This is much simpler, but of course also less flexible.
Please note that if your entity needs a constructor, you need to call ``parent::__construct()`` inside it.

### 4. Other bundles

This bundle register a Doctrine subscriber that listens to ``onFlush`` event with priority 10.
If you use this bundle together with other bundles that register subscribers on the same
event, you could experience some issues in case of higher priority.

If this case even occurs, feel free to open a Pull Request.

### 5. Javascript enhancement

If you want to enhance user experience with a bit of Javascript/AJAX, read this [small cookbook](javascript.md).

Here is a sneak preview of what you'll get.

![tag](https://cloud.githubusercontent.com/assets/179866/7724813/3e1d5b50-fef5-11e4-83f1-e05615518548.png)
