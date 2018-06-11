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
$ composer require beelab/tag-bundle
```

If you still don't use Flex, you'll need to enable bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new Beelab\TagBundle\BeelabTagBundle(),
    ];
}
```
See also [Other bundles](#4-other-bundles) for a note about registering order.

### 2. Configuration

Create a `Tag` entity class.
Using Flex, a contrib recipe is creating such entity for you.
Example:

```php
<?php
// src/Entity/Tag.php
namespace App\Entity;

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

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
```

Following configuration is added by recipe:

```yaml
# config/packages/beelab_tag.yaml

beelab_tag:
    tag_class: App\Entity\Tag
    purge: false
```

> **Warning**: the `purge` option is not mandatory and defaults to `false`. You should use this
> option (with `true` value) only if you want to delete a tag when a taggable entity
> is deleted. You should avoid purging tags if you configured more than a taggable entity,
> since this could lead to constraint violations.

Then you can create some entities that implement `TaggableInteface`.

Suppose you want to use tags on an `Article` entity. You have two options: implementing `TaggableInterface`
(more flexible, showed here), or extending `AbstractTaggable` (simpler, showed later).

```php
<?php
// src/Entity/Article.php
namespace App\Entity;

use Beelab\TagBundle\Tag\TagInterface;
use Beelab\TagBundle\Tag\TaggableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Article implements TaggableInterface
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     */
    private $tags;

    // note: if you generated code, you need to
    // replace "Tag" with "TagInterface" where appropriate

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function addTag(TagInterface $tag): void
    {
        $this->tags[] = $tag;
    }

    public function removeTag(TagInterface $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function hasTag(TagInterface $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function getTags(): iterable
    {
        return $this->tags;
    }

    public function getTagNames(): array
    {
        return empty($this->tagsText) ? [] : array_map('trim', explode(',', $this->tagsText));
    }
}
```

### 3. Usage

Most simple usage is in a Form like this one:

```php
<?php
// src/Form/Type/ArticleFormType.php
namespace Acme\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// ...

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tagsText', TextType::class, ['required' => false, 'label' => 'Tags'])
            // other fields...
        ;
    }
}
```

Then, add a `$tagsText` property to your entity:

```php
<?php
// src/Entity/Article.php

// use...

class Article implements TaggableInterface
{
    // ...

    private $tagsText;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    // ...

    public function setTagsText(?string $tagsText): void
    {
        $this->tagsText = $tagsText;
        $this->updated = new \DateTimeImmutable();
    }

    public function getTagsText(): ?string
    {
        $this->tagsText = implode(', ', $this->tags->toArray());

        return $this->tagsText;
    }

    // ...
}
```

Note that you need to change something in your Entity when `$tagsText` is updated,
otherwise flush is not triggered and tags won't work. In example above, we're using
an `$updated` DateTimeImmutable property.

Instead of implementing `TaggableInterface`, you can extend `AbstractTaggable`, like in this example:

```php
<?php
// src/Entity/Article.php
namespace App\Entity;

use Beelab\TagBundle\Entity\AbstractTaggable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Article extends AbstractTaggable
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    public function setTagsText(?string $tagsText): void
    {
        $this->updated = new \DateTime();
        parent::setTagsText($tagsText);
    }
}
```

This is much simpler, but of course also less flexible.
Please note that if your entity needs a constructor, you need to call `parent::__construct()` inside it.

### 4. Other bundles

This bundle register a Doctrine subscriber that listens to `onFlush` event with priority 10.
If you use this bundle together with other bundles that register subscribers on the same
event, you could experience some issues in case of higher priority.

If this case even occurs, feel free to open a Pull Request.

### 5. Javascript enhancement

If you want to enhance user experience with a bit of Javascript/AJAX, read this [small cookbook](javascript.md).

Here is a sneak preview of what you'll get.

![tag](https://cloud.githubusercontent.com/assets/179866/7724813/3e1d5b50-fef5-11e4-83f1-e05615518548.png)
