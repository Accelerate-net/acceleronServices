package com.accelerate.acceleronServices.reservation.repository;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Repository
public interface ReservationRepository extends JpaRepository<ReservationEntity, Integer> {


    @Modifying
    @Query(value = "select * from z_reservations limit ?1 offset ?2", nativeQuery = true)
    List<ReservationEntity> findAllById(Integer limit, Integer skip);

    @Modifying
    @Query(value = "select * from" +
            " (select * from z_reservations where (userName like concat('%',?1,'%') or userEmail like concat('%',?1,'%')" +
            " or userId like concat('%',?1,'%'))) as search_result limit ?2 offset ?3", nativeQuery = true)
    List<ReservationEntity> findByGivenString(String search, Integer limit, Integer skip);

}
