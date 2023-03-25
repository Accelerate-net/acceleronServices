package com.accelerate.acceleronServices.communication.repository;

import com.accelerate.acceleronServices.communication.model.CommunicationEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

public interface CommunicationRepository extends JpaRepository<CommunicationEntity, Integer> {

    @Override
    @Query(value = "select * from z_communication ", nativeQuery = true)
    List<CommunicationEntity> findAll();

    @Query(value = "select * from z_communication where id = ?1", nativeQuery = true)
    CommunicationEntity getById(int id);

    @Modifying
    @Transactional
    @Query(value = "update z_communication set isDeleted = 1 where id = ?1",nativeQuery = true)
    int DeleteById(int id);
}
