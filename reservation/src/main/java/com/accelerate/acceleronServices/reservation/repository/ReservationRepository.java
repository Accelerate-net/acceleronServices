package com.accelerate.acceleronServices.reservation.repository;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.model.ReservationSummary;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Repository
public interface ReservationRepository extends JpaRepository<ReservationEntity, Integer> {

    @Override
    @Query(value = "select * from z_reservations where isDeleted = 0", nativeQuery = true)
    List<ReservationEntity> findAll();

    @Query(value = "select * from z_reservations where id = ?1 and isDeleted = 0", nativeQuery = true)
    ReservationEntity findById(int id);  // JpaRepository.findById returns optional class(id may not be present). Here it returns null if id is not present.


    @Modifying
    @Query(value = "select * from z_reservations where isDeleted = 0 limit ?1 offset ?2", nativeQuery = true)
    List<ReservationEntity> findAllById(Integer limit, Integer skip);

//    @Modifying
//    @Query(value = "select * from" +
//            " (select * from z_reservations where (userName like concat('%',?1,'%') or userEmail like concat('%',?1,'%')" +
//            " or userId like concat('%',?1,'%'))) as search_result where isDeleted = 0 limit ?2 offset ?3", nativeQuery = true)
//    List<ReservationEntity> findByGivenString(String search, Integer limit, Integer skip);

    @Modifying
    @Query(value = "select * from z_reservations where (userName like concat('%',?1,'%') or userEmail like concat('%',?1,'%')" +
            " or userId like concat('%',?1,'%')) and isDeleted = 0 limit ?2 offset ?3", nativeQuery = true)
    List<ReservationEntity> findByGivenString(String search, Integer limit, Integer skip);


    @Modifying
    @Query(value = "update z_reservations set isDeleted = 1 where id = ?1", nativeQuery = true)
    @Transactional
    int DeleteById(Integer id);


    @Modifying
    @Query(value = "update z_reservations set status = ?2 where id = ?1 and isDeleted = 0", nativeQuery = true)
    @Transactional
    int updateStatus(int id, int status);

//    @Query(value = "SELECT CASE WHEN EXISTS ( SELECT * FROM [z_reservations] WHERE id = ?1 and isDeleted = 0)" +
//            " THEN CAST(1 AS BIT) ELSE CAST(0 AS BIT) END", nativeQuery = true)
//    boolean existsById(int id);

    @Query(value = "select * from z_reservations where userId = ?1 and isDeleted = 0", nativeQuery = true)
    List<ReservationEntity> findByUserId(String userId);

    @Query(value = "select * from z_reservations where outlet = ?1 and date = ?2 and isDeleted = 0", nativeQuery = true)
    List<ReservationEntity> getReservationSummaryPerBranch(String outlet, String date);

}
