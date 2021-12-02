<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AddressRepository extends EntityRepository
{
    public function getAllAddressCount()
    {
        return $this->getEntityManager()
            ->createQuery("Select count(a.id) FROM AppBundle:Address a")
            ->getSingleScalarResult();
    }

    public function getNoPictureCount()
    {
        return $this->getEntityManager()
            ->createQuery("Select count(a.id) FROM AppBundle:Address a WHERE a.picture is Null")
            ->getSingleScalarResult();
    }

    public function getCountryCount()
    {
        return $this->getEntityManager()
            ->createQuery("Select c.name as country, count(a.id) as n FROM AppBundle:Address a
            INNER Join AppBundle:Country c
            Where c.id = a.country
            Group By a.country")
            ->getResult();
    }

    public function getAgeCount()
    {
        $em = $this->getEntityManager();
        $sql = "Select
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  < 21 THEN 1 ELSE 0 END) AS [Under 21],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  BETWEEN 21 AND 30 THEN 1 ELSE 0 END) AS [21 - 30],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  BETWEEN 31 AND 40 THEN 1 ELSE 0 END) AS [31 - 40],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int)  BETWEEN 41 AND 50 THEN 1 ELSE 0 END) AS [41 - 50],
        SUM(CASE WHEN cast(strftime('%Y.%m%d', 'now') - strftime('%Y.%m%d', birthday) as int) > 50 THEN 1 ELSE 0 END) AS [Over 50]
        from address a";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }
}
