package com.accelerate.acceleronServices.smartMenu.repository;

import com.accelerate.acceleronServices.smartMenu.model.ServiceRequestEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

@Repository
public interface ServiceRequestRepository extends JpaRepository<ServiceRequestEntity, Integer> {

    ServiceRequestEntity findById(int requestId);

    @Modifying
    @Query(value = "UPDATE `smart_service_requests` SET `status`= ?2 WHERE `id` = ?1", nativeQuery = true)
    void updateStatus(int requestId, int status);
}
