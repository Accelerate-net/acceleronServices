package com.accelerate.acceleronServices.license.repository;

import com.accelerate.acceleronServices.license.model.LicenseEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Repository
public interface LicenseRepository extends JpaRepository<LicenseEntity, Integer> {

    @Override
    @Query(value = "select * from z_accelerate_license where isDeleted = 0", nativeQuery = true)
    List<LicenseEntity> findAll();
    @Query(value = "select * from z_accelerate_license where id = ?1 and isDeleted = 0", nativeQuery = true)
    LicenseEntity getById(int id);

    @Query(value = "select * from z_accelerate_license where license = ?1 and isDeleted = 0", nativeQuery = true)
    LicenseEntity getByLicense(String license);

    @Modifying
    @Transactional
    @Query(value = "update z_accelerate_license set isDeleted = 1 where id = ?1",nativeQuery = true)
    int DeleteById(int id);


    @Modifying
    @Transactional
    @Query(value = "update z_accelerate_license set machineCustomName = ?2 where license = ?1 and isDeleted = 0",nativeQuery = true)
    int updateMachineCustomName(String license, String machineCustomName);

    @Modifying
    @Transactional
    @Query(value = "update z_accelerate_license set isActive = true where license = ?1 and isDeleted = 0",nativeQuery = true)
    int activateLicense(String license);

    @Modifying
    @Transactional
    @Query(value = "update z_accelerate_license set isActive = false where license = ?1 and isDeleted = 0",nativeQuery = true)
    int deactivateLicense(String license);
}
