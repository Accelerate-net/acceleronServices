package com.accelerate.acceleronServices.repository;

import com.accelerate.acceleronServices.model.ServiceRequestEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

@Repository
public interface ServiceRequestRepository extends JpaRepository<ServiceRequestEntity, Integer> {

    ServiceRequestEntity findById(int requestId);

    @Query(value = "UPDATE `smart_service_requests` SET `status`= ?2 WHERE `id` = ?1", nativeQuery = true)
    void updateStatus(int requestId, int status);
}
